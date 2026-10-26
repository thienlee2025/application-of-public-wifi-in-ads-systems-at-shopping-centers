// assets/js/main.js

/**
 * Shopping Mall WiFi - Main JavaScript
 * Xử lý các chức năng chung cho toàn bộ ứng dụng
 */

// ===== GLOBAL VARIABLES =====
const API_BASE = '/shopping-mall/api';
let chartInstances = {};

// ===== UTILITY FUNCTIONS =====
const utils = {
    // Format số tiền
    formatCurrency: (amount) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    },
    
    // Format ngày tháng
    formatDate: (date, format = 'DD/MM/YYYY') => {
        const d = new Date(date);
        const day = d.getDate().toString().padStart(2, '0');
        const month = (d.getMonth() + 1).toString().padStart(2, '0');
        const year = d.getFullYear();
        
        return format
            .replace('DD', day)
            .replace('MM', month)
            .replace('YYYY', year);
    },
    
    // Format thời gian
    formatDateTime: (date) => {
        const d = new Date(date);
        return d.toLocaleString('vi-VN');
    },
    
    // Hiển thị thông báo
    showAlert: (message, type = 'success') => {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} animate-slide`;
        alertDiv.innerHTML = message;
        
        document.body.prepend(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    },
    
    // Xác thực email
    validateEmail: (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    // Xác thực số điện thoại Việt Nam
    validatePhone: (phone) => {
        const re = /(84|0[3|5|7|8|9])+([0-9]{8})\b/;
        return re.test(phone);
    },
    
    // Debounce function
    debounce: (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Lưu dữ liệu vào localStorage
    saveToStorage: (key, data) => {
        try {
            localStorage.setItem(key, JSON.stringify(data));
            return true;
        } catch (e) {
            console.error('Storage error:', e);
            return false;
        }
    },
    
    // Đọc dữ liệu từ localStorage
    getFromStorage: (key) => {
        try {
            const data = localStorage.getItem(key);
            return data ? JSON.parse(data) : null;
        } catch (e) {
            console.error('Storage error:', e);
            return null;
        }
    }
};

// ===== API FUNCTIONS =====
const api = {
    // Lấy thống kê
    getStats: async (params = {}) => {
        try {
            const queryString = new URLSearchParams(params).toString();
            const response = await fetch(`${API_BASE}/get_stats.php?${queryString}`);
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Lỗi tải dữ liệu');
            }
            
            return data;
        } catch (error) {
            console.error('API Error:', error);
            utils.showAlert('Lỗi kết nối đến server', 'danger');
            return null;
        }
    },
    
    // Gửi form khảo sát
    submitSurvey: async (formData) => {
        try {
            const response = await fetch('/shopping-mall/process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(formData)
            });
            
            const text = await response.text();
            return { success: true, data: text };
        } catch (error) {
            console.error('Submit error:', error);
            return { success: false, error: error.message };
        }
    }
};

// ===== FORM VALIDATION =====
const formValidation = {
    // Validate form khảo sát
    validateSurveyForm: (formData) => {
        const errors = {};
        
        // Kiểm tra họ tên
        if (!formData.fullname || formData.fullname.trim().length < 2) {
            errors.fullname = 'Họ tên phải có ít nhất 2 ký tự';
        }
        
        // Kiểm tra email
        if (!formData.email) {
            errors.email = 'Email không được để trống';
        } else if (!utils.validateEmail(formData.email)) {
            errors.email = 'Email không đúng định dạng';
        }
        
        // Kiểm tra số điện thoại (nếu có)
        if (formData.phone && !utils.validatePhone(formData.phone)) {
            errors.phone = 'Số điện thoại không đúng định dạng Việt Nam';
        }
        
        // Kiểm tra danh mục
        if (!formData.product_search) {
            errors.product_search = 'Vui lòng chọn danh mục quan tâm';
        }
        
        // Kiểm tra mức chi tiêu
        if (!formData.spending) {
            errors.spending = 'Vui lòng chọn mức chi tiêu';
        }
        
        // Kiểm tra xu hướng
        if (!formData.trend) {
            errors.trend = 'Vui lòng chọn xu hướng mua sắm';
        }
        
        return {
            isValid: Object.keys(errors).length === 0,
            errors: errors
        };
    },
    
    // Hiển thị lỗi trên form
    showErrors: (errors) => {
        // Xóa các lỗi cũ
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.error').forEach(el => {
            el.classList.remove('error');
        });
        
        // Hiển thị lỗi mới
        Object.keys(errors).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('error');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = errors[field];
                
                input.parentNode.appendChild(errorDiv);
            }
        });
    }
};

// ===== CHART FUNCTIONS =====
const charts = {
    // Tạo biểu đồ đường
    createLineChart: (canvasId, labels, data, options = {}) => {
        const ctx = document.getElementById(canvasId)?.getContext('2d');
        if (!ctx) return null;
        
        // Hủy chart cũ nếu tồn tại
        if (chartInstances[canvasId]) {
            chartInstances[canvasId].destroy();
        }
        
        chartInstances[canvasId] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: options.label || 'Giá trị',
                    data: data,
                    borderColor: options.borderColor || '#667eea',
                    backgroundColor: options.backgroundColor || 'rgba(102, 126, 234, 0.1)',
                    tension: options.tension || 0.4,
                    fill: options.fill || true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: options.showLegend || false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        return chartInstances[canvasId];
    },
    
    // Tạo biểu đồ tròn
    createPieChart: (canvasId, labels, data, colors = []) => {
        const ctx = document.getElementById(canvasId)?.getContext('2d');
        if (!ctx) return null;
        
        if (chartInstances[canvasId]) {
            chartInstances[canvasId].destroy();
        }
        
        const defaultColors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe'];
        
        chartInstances[canvasId] = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.length ? colors : defaultColors.slice(0, data.length)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        return chartInstances[canvasId];
    },
    
    // Tạo biểu đồ cột
    createBarChart: (canvasId, labels, data, options = {}) => {
        const ctx = document.getElementById(canvasId)?.getContext('2d');
        if (!ctx) return null;
        
        if (chartInstances[canvasId]) {
            chartInstances[canvasId].destroy();
        }
        
        chartInstances[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: options.label || 'Số lượng',
                    data: data,
                    backgroundColor: options.backgroundColor || '#667eea'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        return chartInstances[canvasId];
    }
};

// ===== DOM MANIPULATION =====
const dom = {
    // Tải dữ liệu vào bảng
    loadTableData: (tableId, data, columns) => {
        const table = document.getElementById(tableId);
        if (!table) return;
        
        const tbody = table.querySelector('tbody') || table;
        tbody.innerHTML = '';
        
        data.forEach((row, index) => {
            const tr = document.createElement('tr');
            
            columns.forEach(col => {
                const td = document.createElement('td');
                if (typeof col === 'function') {
                    td.innerHTML = col(row, index);
                } else {
                    td.textContent = row[col] || '';
                }
                tr.appendChild(td);
            });
            
            tbody.appendChild(tr);
        });
    },
    
    // Cập nhật số liệu thống kê
    updateStats: (stats) => {
        Object.keys(stats).forEach(key => {
            const element = document.getElementById(`stat-${key}`);
            if (element) {
                element.textContent = stats[key];
            }
        });
    },
    
    // Hiển thị/ẩn loading
    toggleLoading: (show) => {
        const loader = document.getElementById('global-loader');
        if (loader) {
            loader.style.display = show ? 'flex' : 'none';
        }
    }
};

// ===== EVENT HANDLERS =====
document.addEventListener('DOMContentLoaded', () => {
    console.log('Shopping Mall WiFi App initialized');
    
    // Xử lý form submit
    const surveyForm = document.getElementById('surveyForm');
    if (surveyForm) {
        surveyForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(surveyForm);
            const data = Object.fromEntries(formData.entries());
            
            // Validate form
            const validation = formValidation.validateSurveyForm(data);
            
            if (!validation.isValid) {
                formValidation.showErrors(validation.errors);
                return;
            }
            
            // Disable nút submit
            const submitBtn = surveyForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Đang xử lý...';
            }
            
            // Gửi dữ liệu
            const result = await api.submitSurvey(data);
            
            if (result.success) {
                // Chuyển hướng đến trang cảm ơn
                window.location.href = '/shopping-mall/thanks.html';
            } else {
                utils.showAlert('Có lỗi xảy ra, vui lòng thử lại', 'danger');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'ĐĂNG KÝ NGAY';
                }
            }
        });
    }
    
    // Auto hide alerts
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    });
});

// Export modules
window.utils = utils;
window.api = api;
window.charts = charts;
window.dom = dom;
window.formValidation = formValidation;