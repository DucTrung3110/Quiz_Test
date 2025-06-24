/**
 * Quiz Test System - Custom JavaScript
 */

$(document).ready(function() {
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Auto-hide alerts after 5 seconds
    $('.alert').not('.alert-permanent').delay(5000).fadeOut();
    
    // Confirm delete actions
    $('.delete-confirm').click(function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });
    
    // Form validation
    $('form').submit(function() {
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    });
    
    // Character counter for textareas
    $('textarea[maxlength]').each(function() {
        var $textarea = $(this);
        var maxLength = $textarea.attr('maxlength');
        var $counter = $('<small class="form-text text-muted char-counter">0 / ' + maxLength + ' characters</small>');
        $textarea.after($counter);
        
        $textarea.on('input', function() {
            var currentLength = $(this).val().length;
            $counter.text(currentLength + ' / ' + maxLength + ' characters');
            
            if (currentLength > maxLength * 0.9) {
                $counter.addClass('text-warning').removeClass('text-muted');
            } else {
                $counter.addClass('text-muted').removeClass('text-warning');
            }
        });
    });
    
    // Quiz timer functionality
    if (typeof quizTimeLimit !== 'undefined' && quizTimeLimit > 0) {
        startQuizTimer(quizTimeLimit);
    }
    
    // Auto-save quiz progress
    if ($('#quizForm').length > 0) {
        autoSaveQuizProgress();
    }
    
    // Smooth scrolling for internal links
    $('a[href^="#"]').click(function(e) {
        e.preventDefault();
        var target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 70
            }, 500);
        }
    });
    
    // Dynamic question reordering
    if ($('.question-list').length > 0) {
        initQuestionReordering();
    }
    
});

/**
 * Start quiz timer
 */
function startQuizTimer(timeLimit) {
    var timeRemaining = timeLimit * 60; // Convert to seconds
    
    var timer = setInterval(function() {
        var minutes = Math.floor(timeRemaining / 60);
        var seconds = timeRemaining % 60;
        
        var display = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        $('#timer').html('<i class="fas fa-clock"></i> Time Remaining: <strong>' + display + '</strong>');
        
        // Change color when time is running out
        if (timeRemaining <= 300) { // 5 minutes
            $('#timer').addClass('text-danger');
        } else if (timeRemaining <= 600) { // 10 minutes
            $('#timer').addClass('text-warning');
        }
        
        // Auto-submit when time is up
        if (timeRemaining <= 0) {
            clearInterval(timer);
            $('#timer').html('<strong class="text-danger">Time\'s Up!</strong>');
            
            // Show warning and auto-submit
            if (confirm('Time is up! Your quiz will be submitted automatically.')) {
                $('#quizForm').submit();
            } else {
                // Force submit after 10 seconds
                setTimeout(function() {
                    $('#quizForm').submit();
                }, 10000);
            }
        }
        
        timeRemaining--;
    }, 1000);
}

/**
 * Auto-save quiz progress to localStorage
 */
function autoSaveQuizProgress() {
    var quizId = $('#quizForm').data('quiz-id') || window.location.search.match(/id=(\d+)/)?.[1];
    if (!quizId) return;
    
    var saveKey = 'quiz_progress_' + quizId;
    
    // Load saved progress
    var savedData = localStorage.getItem(saveKey);
    if (savedData) {
        try {
            var progress = JSON.parse(savedData);
            
            // Restore form data
            $('#student_name').val(progress.student_name || '');
            $('#email').val(progress.email || '');
            
            // Restore answers
            if (progress.answers) {
                Object.keys(progress.answers).forEach(function(questionId) {
                    $('input[name="answers[' + questionId + ']"][value="' + progress.answers[questionId] + '"]').prop('checked', true);
                });
            }
        } catch (e) {
            console.error('Error loading saved progress:', e);
        }
    }
    
    // Save progress on change
    $('#quizForm').on('change', 'input, textarea, select', function() {
        var formData = {
            student_name: $('#student_name').val(),
            email: $('#email').val(),
            answers: {}
        };
        
        // Collect answers
        $('input[name^="answers["]:checked').each(function() {
            var name = $(this).attr('name');
            var questionId = name.match(/answers\[(\d+)\]/)[1];
            formData.answers[questionId] = $(this).val();
        });
        
        localStorage.setItem(saveKey, JSON.stringify(formData));
    });
    
    // Clear saved data on successful submit
    $('#quizForm').on('submit', function() {
        localStorage.removeItem(saveKey);
    });
}

/**
 * Initialize question reordering (drag and drop)
 */
function initQuestionReordering() {
    $('.question-list').sortable({
        handle: '.drag-handle',
        axis: 'y',
        update: function(event, ui) {
            var questionIds = [];
            $(this).find('.question-item').each(function(index) {
                var questionId = $(this).data('question-id');
                questionIds.push({
                    id: questionId,
                    order: index + 1
                });
            });
            
            // Update question order via AJAX
            updateQuestionOrder(questionIds);
        }
    });
}

/**
 * Update question order
 */
function updateQuestionOrder(questionIds) {
    $.ajax({
        url: 'ajax/update_question_order.php',
        method: 'POST',
        data: { questions: questionIds },
        success: function(response) {
            if (response.success) {
                showNotification('Question order updated successfully', 'success');
            } else {
                showNotification('Error updating question order', 'error');
            }
        },
        error: function() {
            showNotification('Error updating question order', 'error');
        }
    });
}

/**
 * Show notification
 */
function showNotification(message, type) {
    var alertClass = 'alert-info';
    switch (type) {
        case 'success':
            alertClass = 'alert-success';
            break;
        case 'error':
            alertClass = 'alert-danger';
            break;
        case 'warning':
            alertClass = 'alert-warning';
            break;
    }
    
    var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>');
    
    $('body').append(notification);
    
    // Auto-hide after 3 seconds
    setTimeout(function() {
        notification.fadeOut(function() {
            $(this).remove();
        });
    }, 3000);
}

/**
 * Format time from seconds to MM:SS
 */
function formatTime(seconds) {
    var minutes = Math.floor(seconds / 60);
    var remainingSeconds = seconds % 60;
    return minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Toggle password visibility
 */
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = field.nextElementSibling;
    
    if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

/**
 * Client-side form validation
 */
function validateQuizForm() {
    var isValid = true;
    var errors = [];
    
    // Validate student name
    var studentName = $('#student_name').val().trim();
    if (studentName.length < 2) {
        errors.push('Please enter your full name');
        $('#student_name').addClass('is-invalid');
        isValid = false;
    } else {
        $('#student_name').removeClass('is-invalid');
    }
    
    // Validate email
    var email = $('#email').val().trim();
    if (!isValidEmail(email)) {
        errors.push('Please enter a valid email address');
        $('#email').addClass('is-invalid');
        isValid = false;
    } else {
        $('#email').removeClass('is-invalid');
    }
    
    // Check if at least some questions are answered
    var totalQuestions = $('input[name^="answers["]').length / 4; // 4 options per question
    var answeredQuestions = $('input[name^="answers["]:checked').length;
    
    if (answeredQuestions === 0) {
        errors.push('Please answer at least one question');
        isValid = false;
    } else if (answeredQuestions < totalQuestions) {
        if (!confirm('You have not answered all questions. Do you want to submit anyway?')) {
            isValid = false;
        }
    }
    
    // Show errors
    if (errors.length > 0) {
        var errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
        errors.forEach(function(error) {
            errorHtml += '<li>' + error + '</li>';
        });
        errorHtml += '</ul></div>';
        
        $('.form-errors').remove();
        $('#quizForm').prepend('<div class="form-errors">' + errorHtml + '</div>');
        
        // Scroll to top
        $('html, body').animate({ scrollTop: 0 }, 500);
    }
    
    return isValid;
}

// Attach form validation to quiz form
$(document).on('submit', '#quizForm', function(e) {
    if (!validateQuizForm()) {
        e.preventDefault();
        return false;
    }
});

// Real-time validation
$(document).on('blur', '#student_name, #email', function() {
    var field = $(this);
    var value = field.val().trim();
    
    if (field.attr('id') === 'student_name') {
        if (value.length < 2) {
            field.addClass('is-invalid');
        } else {
            field.removeClass('is-invalid');
        }
    } else if (field.attr('id') === 'email') {
        if (!isValidEmail(value)) {
            field.addClass('is-invalid');
        } else {
            field.removeClass('is-invalid');
        }
    }
});
