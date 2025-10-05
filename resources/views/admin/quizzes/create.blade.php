<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #7C3AED 0%, #8B5CF6 50%, #A855F7 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    Create New Quiz
                </h2>
                <p style="color: rgba(255, 255, 255, 0.9) !important; font-size: 1.1rem !important; font-weight: 500 !important;">
                    Create an interactive quiz for your course or lesson
                </p>
            </div>
        </div>
    </x-slot>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Quiz</h1>
                    <p class="text-gray-600 mt-2">Create an interactive quiz for your course or lesson</p>
                </div>
                <a href="{{ route('admin.quizzes.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Quizzes
                </a>
            </div>
        </div>

        <!-- Workflow Help -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6 mb-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-lightbulb text-blue-600"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">💡 Quiz Creation Workflow</h3>
                    <div class="text-blue-800 space-y-2 text-sm">
                        <p><strong>Option 1:</strong> Create quiz without lesson → Later assign to lesson from quiz list</p>
                        <p><strong>Option 2:</strong> Create lesson first → Then create quiz and select the lesson</p>
                        <p><strong>Note:</strong> Lesson selection is optional. You can always assign quizzes to lessons later!</p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.quizzes.store') }}" method="POST" x-data="quizCreator()">
            @csrf
            
            <!-- Quiz Basic Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Quiz Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Quiz Title *</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               x-model="quiz.title"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               required>
                        @error('title')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quiz_type" class="block text-sm font-medium text-gray-700 mb-2">Primary Question Type</label>
                        <select id="quiz_type" 
                                name="quiz_type" 
                                x-model="quiz.quiz_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  x-model="quiz.description"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course (Optional)</label>
                        <select id="course_id" 
                                name="course_id" 
                                x-model="quiz.course_id"
                                @change="loadLessons()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="lesson_id" class="block text-sm font-medium text-gray-700 mb-2">Lesson (Optional)</label>
                        <select id="lesson_id" 
                                name="lesson_id" 
                                x-model="quiz.lesson_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Select Lesson</option>
                            <template x-for="lesson in availableLessons" :key="lesson.id">
                                <option :value="lesson.id" x-text="lesson.title"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Quiz Settings -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Quiz Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-2">Max Attempts *</label>
                        <input type="number" 
                               id="max_attempts" 
                               name="max_attempts" 
                               value="{{ old('max_attempts', 3) }}"
                               x-model="quiz.max_attempts"
                               min="1" max="10"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               required>
                    </div>

                    <div>
                        <label for="time_limit_minutes" class="block text-sm font-medium text-gray-700 mb-2">Time Limit (Minutes)</label>
                        <input type="number" 
                               id="time_limit_minutes" 
                               name="time_limit_minutes" 
                               value="{{ old('time_limit_minutes') }}"
                               x-model="quiz.time_limit_minutes"
                               min="1" max="300"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <p class="text-sm text-gray-500 mt-1">Leave empty for no time limit</p>
                    </div>

                    <div>
                        <label for="passing_score" class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%) *</label>
                        <input type="number" 
                               id="passing_score" 
                               name="passing_score" 
                               value="{{ old('passing_score', 70) }}"
                               x-model="quiz.passing_score"
                               min="0" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="randomize_questions" 
                               name="randomize_questions" 
                               value="1"
                               x-model="quiz.randomize_questions"
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="randomize_questions" class="ml-2 block text-sm text-gray-700">
                            Randomize Questions
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="show_correct_answers" 
                               name="show_correct_answers" 
                               value="1"
                               x-model="quiz.show_correct_answers"
                               checked
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="show_correct_answers" class="ml-2 block text-sm text-gray-700">
                            Show Correct Answers
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_required" 
                               name="is_required" 
                               value="1"
                               x-model="quiz.is_required"
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="is_required" class="ml-2 block text-sm text-gray-700">
                            Required to Progress
                        </label>
                    </div>
                </div>
            </div>

            <!-- Questions Section -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Questions</h2>
                    <button type="button" 
                            @click="addQuestion()"
                            class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Question
                    </button>
                </div>

                <div x-show="quiz.questions.length === 0" class="text-center py-8 text-gray-500">
                    <i class="fas fa-question-circle text-4xl mb-4"></i>
                    <p>No questions added yet. Click "Add Question" to get started.</p>
                </div>

                <template x-for="(question, index) in quiz.questions" :key="question.id">
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900" x-text="`Question ${index + 1}`"></h3>
                            <button type="button" 
                                    @click="removeQuestion(index)"
                                    class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question Text *</label>
                                <textarea x-model="question.question"
                                          :name="`questions[${index}][question]`"
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                          required></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question Type</label>
                                <select x-model="question.question_type"
                                        :name="`questions[${index}][question_type]`"
                                        @change="updateQuestionType(index)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="short_answer">Short Answer</option>
                                    <option value="essay">Essay</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Points *</label>
                                <input type="number" 
                                       x-model="question.points"
                                       :name="`questions[${index}][points]`"
                                       min="0.1" max="100" step="0.1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                       required>
                            </div>
                        </div>

                        <!-- Multiple Choice Options -->
                        <div x-show="question.question_type === 'multiple_choice'" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Answer Options</label>
                            <template x-for="(option, optionIndex) in question.options" :key="optionIndex">
                                <div class="flex items-center mb-2">
                                    <input type="radio" 
                                           :name="`question_${index}_correct`"
                                           :value="optionIndex"
                                           @change="setCorrectAnswer(index, optionIndex)"
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                                    <input type="text" 
                                           x-model="option.text"
                                           :name="`questions[${index}][options][${optionIndex}]`"
                                           placeholder="Enter option text"
                                           class="ml-2 flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <button type="button" 
                                            @click="removeOption(index, optionIndex)"
                                            x-show="question.options.length > 2"
                                            class="ml-2 text-red-600 hover:text-red-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                            <button type="button" 
                                    @click="addOption(index)"
                                    class="text-purple-600 hover:text-purple-800 text-sm">
                                <i class="fas fa-plus mr-1"></i>Add Option
                            </button>
                        </div>

                        <!-- True/False Options -->
                        <div x-show="question.question_type === 'true_false'" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" 
                                           :name="`question_${index}_tf`"
                                           value="true"
                                           @change="question.correct_answers = ['true']"
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                                    <span class="ml-2">True</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" 
                                           :name="`question_${index}_tf`"
                                           value="false"
                                           @change="question.correct_answers = ['false']"
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                                    <span class="ml-2">False</span>
                                </label>
                            </div>
                        </div>

                        <!-- Short Answer -->
                        <div x-show="question.question_type === 'short_answer'" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answers (one per line)</label>
                            <textarea x-model="question.correct_answers_text"
                                      @input="updateShortAnswers(index)"
                                      rows="3"
                                      placeholder="Enter possible correct answers, one per line"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                        </div>

                        <!-- Hidden inputs for correct answers -->
                        <template x-for="(answer, answerIndex) in question.correct_answers" :key="answerIndex">
                            <input type="hidden" 
                                   :name="`questions[${index}][correct_answers][${answerIndex}]`" 
                                   :value="answer">
                        </template>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
                            <textarea x-model="question.explanation"
                                      :name="`questions[${index}][explanation]`"
                                      rows="2"
                                      placeholder="Explain why this answer is correct"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Submit Button -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex justify-between">
                    <a href="{{ route('admin.quizzes.index') }}" 
                       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Quiz
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function quizCreator() {
    return {
        quiz: {
            title: '',
            description: '',
            course_id: '',
            lesson_id: '',
            quiz_type: 'multiple_choice',
            max_attempts: 3,
            time_limit_minutes: '',
            passing_score: 70,
            randomize_questions: false,
            show_correct_answers: true,
            is_required: false,
            questions: []
        },
        availableLessons: [],
        
        init() {
            this.addQuestion();
        },
        
        addQuestion() {
            this.quiz.questions.push({
                id: Date.now(),
                question: '',
                question_type: 'multiple_choice',
                options: [
                    { text: '' },
                    { text: '' }
                ],
                correct_answers: [],
                correct_answers_text: '',
                explanation: '',
                points: 1
            });
        },
        
        removeQuestion(index) {
            this.quiz.questions.splice(index, 1);
        },
        
        updateQuestionType(index) {
            const question = this.quiz.questions[index];
            question.correct_answers = [];
            
            if (question.question_type === 'multiple_choice') {
                question.options = [{ text: '' }, { text: '' }];
            } else if (question.question_type === 'true_false') {
                question.options = [];
            }
        },
        
        addOption(questionIndex) {
            this.quiz.questions[questionIndex].options.push({ text: '' });
        },
        
        removeOption(questionIndex, optionIndex) {
            this.quiz.questions[questionIndex].options.splice(optionIndex, 1);
        },
        
        setCorrectAnswer(questionIndex, optionIndex) {
            this.quiz.questions[questionIndex].correct_answers = [optionIndex.toString()];
        },
        
        updateShortAnswers(index) {
            const question = this.quiz.questions[index];
            const answers = question.correct_answers_text.split('\n').filter(a => a.trim());
            question.correct_answers = answers;
        },
        
        async loadLessons() {
            if (!this.quiz.course_id) {
                this.availableLessons = [];
                return;
            }
            
            try {
                const response = await fetch(`{{ route('admin.api.lessons-for-course') }}?course_id=${this.quiz.course_id}`);
                const data = await response.json();
                this.availableLessons = data.lessons || [];
            } catch (error) {
                console.error('Error loading lessons:', error);
                this.availableLessons = [];
            }
        }
    }
}
</script>
</x-app-layout>
