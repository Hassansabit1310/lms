<x-app-layout>
    <x-slot name="header">
        <div style="background: linear-gradient(135deg, #7C3AED 0%, #8B5CF6 50%, #A855F7 100%) !important; min-height: 120px !important; display: block !important; width: 100% !important;">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
                <h2 style="color: white !important; font-size: 2.5rem !important; font-weight: 900 !important; margin-bottom: 0.5rem !important; text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;">
                    {{ $quiz->title }}
                </h2>
                <p style="color: rgba(255, 255, 255, 0.9) !important; font-size: 1.1rem !important; font-weight: 500 !important;">
                    Quiz Details & Statistics
                </p>
            </div>
        </div>
    </x-slot>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $quiz->title }}</h1>
                    <p class="text-gray-600 mt-2">{{ $quiz->description }}</p>
                    <div class="flex items-center mt-4 space-x-4">
                        <span class="badge {{ $quiz->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($quiz->is_required)
                            <span class="badge badge-warning">Required</span>
                        @endif
                        <span class="text-sm text-gray-500">
                            Created {{ $quiz->created_at->format('M j, Y') }}
                        </span>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Quiz
                    </a>
                    <a href="{{ route('admin.quizzes.index') }}" 
                       class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Quizzes
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quiz Details -->
            <div class="lg:col-span-2">
                <!-- Quiz Settings -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Quiz Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                            <p class="text-gray-900">{{ $quiz->course->title ?? 'Standalone Quiz' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lesson</label>
                            <p class="text-gray-900">{{ $quiz->lesson->title ?? 'Not assigned' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Type</label>
                            <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $quiz->quiz_type)) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Attempts</label>
                            <p class="text-gray-900">{{ $quiz->max_attempts }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Time Limit</label>
                            <p class="text-gray-900">{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' minutes' : 'No limit' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Passing Score</label>
                            <p class="text-gray-900">{{ $quiz->passing_score }}%</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Randomize Questions</label>
                            <p class="text-gray-900">{{ $quiz->randomize_questions ? 'Yes' : 'No' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Show Correct Answers</label>
                            <p class="text-gray-900">{{ $quiz->show_correct_answers ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Questions -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Questions ({{ $quiz->questions->count() }})</h2>
                    
                    @if($quiz->questions->count() > 0)
                        <div class="space-y-4">
                            @foreach($quiz->questions as $index => $question)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="font-semibold text-gray-900">Question {{ $index + 1 }}</h3>
                                        <div class="flex items-center space-x-2">
                                            <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                            <span class="text-sm text-gray-600">{{ $question->points }} pts</span>
                                        </div>
                                    </div>
                                    
                                    <p class="text-gray-800 mb-3">{{ $question->question }}</p>
                                    
                                    @if($question->question_type === 'multiple_choice' && $question->options)
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Options:</label>
                                            <ul class="space-y-1">
                                                @foreach($question->options as $optionIndex => $option)
                                                    <li class="flex items-center">
                                                        <span class="w-6 h-6 rounded-full {{ in_array($optionIndex, $question->correct_answers) ? 'bg-green-500 text-white' : 'bg-gray-200' }} flex items-center justify-center text-sm font-medium mr-2">
                                                            {{ chr(65 + $optionIndex) }}
                                                        </span>
                                                        <span class="{{ in_array($optionIndex, $question->correct_answers) ? 'font-semibold text-green-700' : '' }}">
                                                            {{ $option }}
                                                        </span>
                                                        @if(in_array($optionIndex, $question->correct_answers))
                                                            <i class="fas fa-check text-green-500 ml-2"></i>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @elseif($question->question_type === 'true_false')
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer:</label>
                                            <span class="badge badge-success">{{ ucfirst($question->correct_answers[0] ?? 'Not set') }}</span>
                                        </div>
                                    @elseif(in_array($question->question_type, ['short_answer', 'essay']))
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Acceptable Answers:</label>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($question->correct_answers as $answer)
                                                    <span class="badge badge-success">{{ $answer }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($question->explanation)
                                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                            <label class="block text-sm font-medium text-blue-700 mb-1">Explanation:</label>
                                            <p class="text-blue-800 text-sm">{{ $question->explanation }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-question-circle text-4xl mb-4"></i>
                            <p>No questions added yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Stats</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Questions</span>
                            <span class="font-semibold text-gray-900">{{ $quiz->questions->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Points</span>
                            <span class="font-semibold text-gray-900">{{ $quiz->getTotalPoints() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Attempts</span>
                            <span class="font-semibold text-gray-900">{{ $statistics['total_attempts'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Completion Rate</span>
                            <span class="font-semibold text-gray-900">{{ number_format($statistics['completion_rate'], 1) }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Average Score</span>
                            <span class="font-semibold text-gray-900">{{ number_format($statistics['average_score'], 1) }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Pass Rate</span>
                            <span class="font-semibold text-gray-900">{{ number_format($statistics['pass_rate'], 1) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.quizzes.duplicate', $quiz) }}" 
                           class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-center block">
                            <i class="fas fa-copy mr-2"></i>Duplicate Quiz
                        </a>
                        
                        @if($statistics['total_attempts'] > 0)
                            <a href="{{ route('admin.quizzes.export-results', $quiz) }}" 
                               class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-center block">
                                <i class="fas fa-download mr-2"></i>Export Results
                            </a>
                        @endif
                        
                        <button type="button" 
                                onclick="toggleQuizStatus({{ $quiz->id }})"
                                class="w-full bg-{{ $quiz->is_active ? 'orange' : 'green' }}-600 text-white px-4 py-2 rounded-lg hover:bg-{{ $quiz->is_active ? 'orange' : 'green' }}-700 transition-colors">
                            <i class="fas fa-{{ $quiz->is_active ? 'pause' : 'play' }} mr-2"></i>
                            {{ $quiz->is_active ? 'Deactivate' : 'Activate' }} Quiz
                        </button>
                        
                        <form action="{{ route('admin.quizzes.destroy', $quiz) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this quiz? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash mr-2"></i>Delete Quiz
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Recent Attempts -->
                @if($quiz->attempts->count() > 0)
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Attempts</h3>
                        <div class="space-y-3">
                            @foreach($quiz->attempts->take(5) as $attempt)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $attempt->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $attempt->created_at->format('M j, g:i A') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold {{ $attempt->is_passed ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($attempt->score, 1) }}%
                                        </span>
                                        <p class="text-xs text-gray-500">
                                            {{ $attempt->is_passed ? 'Passed' : 'Failed' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
async function toggleQuizStatus(quizId) {
    try {
        const response = await fetch(`/admin/quizzes/${quizId}/toggle-active`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to update quiz status');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to update quiz status');
    }
}
</script>
</x-app-layout>
