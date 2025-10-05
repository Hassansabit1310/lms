/**
 * Quiz Player - Interactive Quiz Taking System
 */
class QuizPlayer {
    constructor(options = {}) {
        this.quizId = options.quizId;
        this.lessonId = options.lessonId;
        this.courseId = options.courseId;
        this.attempt = null;
        this.questions = [];
        this.currentQuestionIndex = 0;
        this.answers = {};
        this.timeRemaining = -1;
        this.timerInterval = null;
        this.autoSaveInterval = null;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.setupAutoSave();
    }
    
    bindEvents() {
        // Start quiz button
        document.addEventListener('click', (e) => {
            if (e.target.matches('.start-quiz-btn')) {
                e.preventDefault();
                const quizId = e.target.dataset.quizId;
                console.log('Starting quiz:', quizId, 'Button:', e.target);
                this.startQuiz(quizId);
            }
        });
        
        // Answer selection
        document.addEventListener('change', (e) => {
            if (e.target.matches('.quiz-answer-input')) {
                this.saveAnswer(e.target);
            }
        });
        
        // Navigation buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.quiz-prev-btn')) {
                e.preventDefault();
                this.previousQuestion();
            } else if (e.target.matches('.quiz-next-btn')) {
                e.preventDefault();
                this.nextQuestion();
            } else if (e.target.matches('.quiz-submit-btn')) {
                e.preventDefault();
                this.submitQuiz();
            }
        });
    }
    
    async startQuiz(quizId) {
        try {
            this.quizId = quizId; // Set the quiz ID for later use
            this.showLoading('Starting quiz...');
            
            const url = `/courses/${this.courseId}/lessons/${this.lessonId}/quiz/${quizId}/start`;
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.attempt = data.attempt;
                this.questions = data.questions;
                this.timeRemaining = data.time_remaining;
                this.currentQuestionIndex = 0;
                this.answers = this.attempt.answers || {};
                
                this.renderQuizInterface();
                this.startTimer();
            } else {
                this.showError(data.error || 'Failed to start quiz');
            }
        } catch (error) {
            console.error('Error starting quiz:', error);
            this.showError('Failed to start quiz. Please try again.');
        }
    }
    
    renderQuizInterface() {
        console.log('Rendering quiz interface for quiz:', this.quizId);
        const quizContainer = document.querySelector(`[data-quiz-id="${this.quizId}"]`);
        console.log('Found container:', quizContainer);
        
        if (!quizContainer) {
            console.error('Quiz container not found for quizId:', this.quizId);
            console.log('Available containers:', document.querySelectorAll('[data-quiz-id]'));
            return;
        }
        
        // Remove loading overlay
        const loadingOverlay = quizContainer.querySelector('.quiz-loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
        
        const html = `
            <div class="quiz-player">
                <!-- Quiz Header -->
                <div class="quiz-header bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-purple-900">Quiz in Progress</h3>
                            <p class="text-sm text-purple-600">Question ${this.currentQuestionIndex + 1} of ${this.questions.length}</p>
                        </div>
                        <div class="quiz-timer" ${this.timeRemaining === -1 ? 'style="display: none;"' : ''}>
                            <div class="flex items-center text-purple-600">
                                <i class="fas fa-clock mr-2"></i>
                                <span class="timer-display font-mono text-lg">--:--</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="bg-purple-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" 
                                 style="width: ${((this.currentQuestionIndex + 1) / this.questions.length) * 100}%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Question Area -->
                <div class="quiz-question-area">
                    ${this.renderCurrentQuestion()}
                </div>
                
                <!-- Navigation -->
                <div class="quiz-navigation mt-6 flex items-center justify-between">
                    <button type="button" 
                            class="quiz-prev-btn btn btn-secondary ${this.currentQuestionIndex === 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                            ${this.currentQuestionIndex === 0 ? 'disabled' : ''}>
                        <i class="fas fa-chevron-left mr-2"></i>Previous
                    </button>
                    
                    <div class="question-indicators flex space-x-2">
                        ${this.questions.map((_, index) => `
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                        ${index === this.currentQuestionIndex ? 'bg-purple-600 text-white' : 
                                          this.answers[this.questions[index].id] ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600'}">
                                ${index + 1}
                            </div>
                        `).join('')}
                    </div>
                    
                    ${this.currentQuestionIndex === this.questions.length - 1 ? `
                        <button type="button" class="quiz-submit-btn btn btn-success">
                            <i class="fas fa-check mr-2"></i>Submit Quiz
                        </button>
                    ` : `
                        <button type="button" class="quiz-next-btn btn btn-primary">
                            Next<i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    `}
                </div>
            </div>
        `;
        
        quizContainer.innerHTML = html;
        this.updateTimer();
    }
    
    renderCurrentQuestion() {
        const question = this.questions[this.currentQuestionIndex];
        if (!question) return '';
        
        let html = `
            <div class="question-card bg-white border border-gray-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    Question ${this.currentQuestionIndex + 1}
                </h4>
                <div class="question-text text-gray-800 mb-6">
                    ${question.question.replace(/\n/g, '<br>')}
                </div>
                
                <div class="answer-options">
        `;
        
        const currentAnswer = this.answers[question.id];
        
        switch (question.question_type) {
            case 'multiple_choice':
                question.options.forEach((option, index) => {
                    html += `
                        <label class="answer-option flex items-center p-3 border border-gray-200 rounded-lg mb-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" 
                                   name="question_${question.id}" 
                                   value="${index}"
                                   class="quiz-answer-input h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                   data-question-id="${question.id}"
                                   ${currentAnswer == index ? 'checked' : ''}>
                            <span class="ml-3 text-gray-800">${option}</span>
                        </label>
                    `;
                });
                break;
                
            case 'true_false':
                ['true', 'false'].forEach(value => {
                    html += `
                        <label class="answer-option flex items-center p-3 border border-gray-200 rounded-lg mb-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" 
                                   name="question_${question.id}" 
                                   value="${value}"
                                   class="quiz-answer-input h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                   data-question-id="${question.id}"
                                   ${currentAnswer === value ? 'checked' : ''}>
                            <span class="ml-3 text-gray-800 capitalize">${value}</span>
                        </label>
                    `;
                });
                break;
                
            case 'short_answer':
                html += `
                    <textarea name="question_${question.id}"
                              class="quiz-answer-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                              data-question-id="${question.id}"
                              rows="4"
                              placeholder="Enter your answer...">${currentAnswer || ''}</textarea>
                `;
                break;
                
            case 'essay':
                html += `
                    <textarea name="question_${question.id}"
                              class="quiz-answer-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                              data-question-id="${question.id}"
                              rows="8"
                              placeholder="Write your essay answer...">${currentAnswer || ''}</textarea>
                `;
                break;
        }
        
        html += `
                </div>
            </div>
        `;
        
        return html;
    }
    
    async saveAnswer(input) {
        const questionId = input.dataset.questionId;
        let answer;
        
        if (input.type === 'radio' && input.checked) {
            answer = input.value;
        } else if (input.type === 'textarea') {
            answer = input.value;
        } else {
            return;
        }
        
        this.answers[questionId] = answer;
        
        // Auto-save to server
        try {
            await fetch(`/courses/${this.courseId}/lessons/${this.lessonId}/quiz/${this.quizId}/save-answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    attempt_id: this.attempt.id,
                    question_id: questionId,
                    answer: answer
                })
            });
        } catch (error) {
            console.error('Error saving answer:', error);
        }
        
        // Update progress indicators
        this.updateProgressIndicators();
    }
    
    previousQuestion() {
        if (this.currentQuestionIndex > 0) {
            this.currentQuestionIndex--;
            this.renderQuizInterface();
        }
    }
    
    nextQuestion() {
        if (this.currentQuestionIndex < this.questions.length - 1) {
            this.currentQuestionIndex++;
            this.renderQuizInterface();
        }
    }
    
    async submitQuiz() {
        if (!confirm('Are you sure you want to submit your quiz? This action cannot be undone.')) {
            return;
        }
        
        try {
            this.showLoading('Submitting quiz...');
            
            const response = await fetch(`/courses/${this.courseId}/lessons/${this.lessonId}/quiz/${this.quizId}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    attempt_id: this.attempt.id,
                    answers: this.answers
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showResults(data);
            } else {
                this.showError(data.error || 'Failed to submit quiz');
            }
        } catch (error) {
            console.error('Error submitting quiz:', error);
            this.showError('Failed to submit quiz. Please try again.');
        }
    }
    
    showResults(data) {
        this.stopTimer();
        
        const quizContainer = document.querySelector(`[data-quiz-id="${this.quizId}"]`);
        if (!quizContainer) return;
        
        const results = data.results;
        const attempt = data.attempt;
        
        let html = `
            <div class="quiz-results">
                <div class="results-header text-center p-6 ${attempt.is_passed ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'} border rounded-lg mb-6">
                    <div class="text-6xl mb-4">
                        ${attempt.is_passed ? '🎉' : '😔'}
                    </div>
                    <h3 class="text-2xl font-bold ${attempt.is_passed ? 'text-green-800' : 'text-red-800'} mb-2">
                        ${attempt.is_passed ? 'Congratulations!' : 'Keep Trying!'}
                    </h3>
                    <p class="text-lg ${attempt.is_passed ? 'text-green-600' : 'text-red-600'}">
                        You scored ${Math.round(results.percentage)}%
                    </p>
                    <div class="mt-4 text-sm ${attempt.is_passed ? 'text-green-600' : 'text-red-600'}">
                        ${results.points_earned} out of ${results.points_possible} points
                    </div>
                </div>
                
                <div class="results-stats grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="stat-card bg-white border border-gray-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600">${Math.round(results.percentage)}%</div>
                        <div class="text-sm text-gray-600">Final Score</div>
                    </div>
                    <div class="stat-card bg-white border border-gray-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">${Object.values(results.question_results).filter(r => r.is_correct).length}</div>
                        <div class="text-sm text-gray-600">Correct Answers</div>
                    </div>
                    <div class="stat-card bg-white border border-gray-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-600">${Math.round(attempt.time_spent_seconds / 60)}</div>
                        <div class="text-sm text-gray-600">Minutes Taken</div>
                    </div>
                </div>
        `;
        
        if (data.show_correct_answers) {
            html += `
                <div class="question-review">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Question Review</h4>
            `;
            
            this.questions.forEach((question, index) => {
                const result = results.question_results[question.id];
                html += `
                    <div class="question-review-item border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start justify-between mb-3">
                            <h5 class="font-medium text-gray-900">Question ${index + 1}</h5>
                            <span class="badge ${result.is_correct ? 'badge-success' : 'badge-danger'}">
                                ${result.is_correct ? 'Correct' : 'Incorrect'}
                            </span>
                        </div>
                        <p class="text-gray-800 mb-3">${question.question}</p>
                        <div class="text-sm">
                            <div class="mb-2">
                                <strong>Your Answer:</strong> 
                                <span class="${result.is_correct ? 'text-green-600' : 'text-red-600'}">
                                    ${this.formatAnswer(question, result.user_answer)}
                                </span>
                            </div>
                            ${!result.is_correct ? `
                                <div class="mb-2">
                                    <strong>Correct Answer:</strong> 
                                    <span class="text-green-600">
                                        ${this.formatAnswer(question, result.correct_answer)}
                                    </span>
                                </div>
                            ` : ''}
                            ${question.explanation ? `
                                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                    <strong>Explanation:</strong> ${question.explanation}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
        }
        
        if (data.can_retake) {
            html += `
                <div class="retake-section text-center mt-6">
                    <p class="text-gray-600 mb-4">You have ${data.attempts_remaining} attempt(s) remaining.</p>
                    <button type="button" class="start-quiz-btn btn btn-primary" data-quiz-id="${this.quizId}">
                        <i class="fas fa-redo mr-2"></i>Retake Quiz
                    </button>
                </div>
            `;
        }
        
        html += '</div>';
        
        quizContainer.innerHTML = html;
    }
    
    formatAnswer(question, answer) {
        if (question.question_type === 'multiple_choice') {
            return question.options[answer] || 'No answer';
        } else if (question.question_type === 'true_false') {
            return answer ? answer.charAt(0).toUpperCase() + answer.slice(1) : 'No answer';
        } else {
            return answer || 'No answer provided';
        }
    }
    
    startTimer() {
        if (this.timeRemaining === -1) return;
        
        this.timerInterval = setInterval(() => {
            this.timeRemaining--;
            this.updateTimer();
            
            if (this.timeRemaining <= 0) {
                this.timeExpired();
            }
        }, 1000);
    }
    
    stopTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }
    
    updateTimer() {
        const timerDisplay = document.querySelector('.timer-display');
        if (!timerDisplay || this.timeRemaining === -1) return;
        
        const minutes = Math.floor(this.timeRemaining / 60);
        const seconds = this.timeRemaining % 60;
        
        timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Change color when time is running low
        const timerContainer = timerDisplay.closest('.quiz-timer');
        if (this.timeRemaining <= 300) { // 5 minutes
            timerContainer.classList.add('text-red-600');
            timerContainer.classList.remove('text-purple-600');
        }
    }
    
    timeExpired() {
        this.stopTimer();
        alert('Time has expired! Your quiz will be automatically submitted.');
        this.submitQuiz();
    }
    
    setupAutoSave() {
        // Auto-save every 30 seconds
        this.autoSaveInterval = setInterval(() => {
            if (this.attempt && Object.keys(this.answers).length > 0) {
                // Auto-save is handled in saveAnswer method
            }
        }, 30000);
    }
    
    updateProgressIndicators() {
        const indicators = document.querySelectorAll('.question-indicators > div');
        indicators.forEach((indicator, index) => {
            const question = this.questions[index];
            if (this.answers[question.id]) {
                indicator.className = indicator.className.replace(/bg-\w+-\d+/g, '').replace(/text-\w+-\d+/g, '') + ' bg-green-500 text-white';
            }
        });
    }
    
    showLoading(message) {
        // Find any quiz container or create a temporary loading area
        let quizContainer = document.querySelector(`[data-quiz-id="${this.quizId}"]`);
        
        if (!quizContainer) {
            // If no specific container, look for any quiz container
            quizContainer = document.querySelector('.quiz-content-block');
        }
        
        if (quizContainer) {
            // Create a loading overlay instead of replacing content
            const existingOverlay = quizContainer.querySelector('.quiz-loading-overlay');
            if (existingOverlay) {
                existingOverlay.remove();
            }
            
            const overlay = document.createElement('div');
            overlay.className = 'quiz-loading-overlay';
            overlay.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.9);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
            `;
            overlay.innerHTML = `
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">${message}</p>
                </div>
            `;
            
            quizContainer.style.position = 'relative';
            quizContainer.appendChild(overlay);
        }
    }
    
    showError(message) {
        const quizContainer = document.querySelector(`[data-quiz-id="${this.quizId}"]`);
        if (quizContainer) {
            quizContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    ${message}
                </div>
            `;
        }
    }
}

// Initialize quiz player when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get course and lesson IDs from the page
    const courseId = document.querySelector('meta[name="course-id"]')?.content;
    const lessonId = document.querySelector('meta[name="lesson-id"]')?.content;
    
    if (courseId && lessonId) {
        window.quizPlayer = new QuizPlayer({
            courseId: courseId,
            lessonId: lessonId
        });
    }
});
