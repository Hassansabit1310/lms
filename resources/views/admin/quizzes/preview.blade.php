<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quiz Preview: {{ $quiz->title }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.quizzes.edit', $quiz) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Quiz
                </a>
                <a href="{{ route('admin.quizzes.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Quizzes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Quiz Header -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $quiz->title }}</h1>
                        @if($quiz->description)
                            <p class="text-gray-700 text-lg leading-relaxed mb-4">{{ $quiz->description }}</p>
                        @endif
                        
                        <div class="flex items-center space-x-6 text-sm text-gray-600">
                            @if($quiz->course)
                                <div class="flex items-center">
                                    <i class="fas fa-book mr-2 text-blue-500"></i>
                                    <span>Course: {{ $quiz->course->title }}</span>
                                </div>
                            @endif
                            @if($quiz->lesson)
                                <div class="flex items-center">
                                    <i class="fas fa-play mr-2 text-green-500"></i>
                                    <span>Lesson: {{ $quiz->lesson->title }}</span>
                                </div>
                            @endif
                            <div class="flex items-center">
                                <i class="fas fa-{{ $quiz->is_active ? 'check-circle text-green-500' : 'times-circle text-red-500' }} mr-2"></i>
                                <span>{{ $quiz->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-4 border border-purple-200">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-900">{{ $quiz->questions->count() }}</div>
                            <div class="text-sm text-purple-700">Questions</div>
                        </div>
                    </div>
                </div>

                <!-- Quiz Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <div class="text-lg font-semibold text-gray-900">{{ $quiz->max_attempts }}</div>
                        <div class="text-sm text-gray-600">Max Attempts</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-semibold text-gray-900">
                            @if($quiz->time_limit_minutes)
                                {{ $quiz->time_limit_minutes }} min
                            @else
                                No Limit
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">Time Limit</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-semibold text-gray-900">{{ $quiz->passing_score }}%</div>
                        <div class="text-sm text-gray-600">Passing Score</div>
                    </div>
                </div>

                <!-- Quiz Options -->
                <div class="mt-6 flex flex-wrap gap-4">
                    @if($quiz->randomize_questions)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-random mr-1"></i>
                            Randomized Questions
                        </span>
                    @endif
                    @if($quiz->show_correct_answers)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>
                            Show Correct Answers
                        </span>
                    @endif
                    @if($quiz->is_required)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-exclamation mr-1"></i>
                            Required
                        </span>
                    @endif
                </div>
            </div>

            <!-- Questions -->
            @if($quiz->questions->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Questions</h2>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        @foreach($quiz->questions as $index => $question)
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-semibold text-purple-700">{{ $index + 1 }}</span>
                                    </div>
                                    
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $question->question_text }}</h3>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $question->question_type) }}</span>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    {{ $question->points }} pts
                                                </span>
                                            </div>
                                        </div>

                                        @if($question->question_type === 'multiple_choice')
                                            <div class="space-y-2">
                                                @foreach($question->options as $optionKey => $optionValue)
                                                    <div class="flex items-center p-3 rounded-lg {{ in_array($optionKey, (array)$question->correct_answers) ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                                                        <div class="flex-shrink-0 mr-3">
                                                            @if(in_array($optionKey, (array)$question->correct_answers))
                                                                <i class="fas fa-check-circle text-green-500"></i>
                                                            @else
                                                                <i class="far fa-circle text-gray-400"></i>
                                                            @endif
                                                        </div>
                                                        <span class="text-gray-900">{{ $optionValue }}</span>
                                                        @if(in_array($optionKey, (array)$question->correct_answers))
                                                            <span class="ml-auto text-xs font-medium text-green-700">Correct Answer</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($question->question_type === 'true_false')
                                            <div class="space-y-2">
                                                <div class="flex items-center p-3 rounded-lg {{ $question->correct_answers[0] === 'true' ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                                                    <div class="flex-shrink-0 mr-3">
                                                        @if($question->correct_answers[0] === 'true')
                                                            <i class="fas fa-check-circle text-green-500"></i>
                                                        @else
                                                            <i class="far fa-circle text-gray-400"></i>
                                                        @endif
                                                    </div>
                                                    <span class="text-gray-900">True</span>
                                                    @if($question->correct_answers[0] === 'true')
                                                        <span class="ml-auto text-xs font-medium text-green-700">Correct Answer</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center p-3 rounded-lg {{ $question->correct_answers[0] === 'false' ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                                                    <div class="flex-shrink-0 mr-3">
                                                        @if($question->correct_answers[0] === 'false')
                                                            <i class="fas fa-check-circle text-green-500"></i>
                                                        @else
                                                            <i class="far fa-circle text-gray-400"></i>
                                                        @endif
                                                    </div>
                                                    <span class="text-gray-900">False</span>
                                                    @if($question->correct_answers[0] === 'false')
                                                        <span class="ml-auto text-xs font-medium text-green-700">Correct Answer</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($question->question_type === 'short_answer')
                                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                                <div class="text-sm text-green-700 mb-1">Correct Answer(s):</div>
                                                <div class="font-medium text-green-900">
                                                    @foreach((array)$question->correct_answers as $answer)
                                                        <span class="inline-block bg-green-100 px-2 py-1 rounded mr-2 mb-1">{{ $answer }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if($question->explanation)
                                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                                <div class="text-sm text-blue-700 mb-1">Explanation:</div>
                                                <div class="text-blue-900">{{ $question->explanation }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-question-circle text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Questions Added</h3>
                    <p class="text-gray-600 mb-6">This quiz doesn't have any questions yet.</p>
                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" 
                       class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Questions
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
