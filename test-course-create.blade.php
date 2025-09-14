<!DOCTYPE html>
<html>
<head>
    <title>Test Course Create</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Course Creation Form</h1>
        
        <div x-data="{ currentStep: 0, steps: [{title: 'Basic'}, {title: 'Content'}, {title: 'Pricing'}, {title: 'SEO'}] }">
            <div>
                <p>Current Step: <span x-text="currentStep"></span></p>
                <p>Total Steps: <span x-text="steps.length"></span></p>
            </div>
            
            <div>
                <h3>Steps:</h3>
                <template x-for="(step, index) in steps" :key="index">
                    <div>
                        <span x-text="index + 1"></span>: <span x-text="step.title"></span>
                        <span x-show="currentStep === index"> (Current)</span>
                    </div>
                </template>
            </div>
            
            <div>
                <button @click="currentStep = Math.max(0, currentStep - 1)" :disabled="currentStep === 0">Previous</button>
                <button @click="currentStep = Math.min(steps.length - 1, currentStep + 1)" :disabled="currentStep === steps.length - 1">Next</button>
            </div>
            
            <!-- Step content -->
            <div x-show="currentStep === 0">
                <h3>Step 1: Basic Information</h3>
                <p>This is the basic information step.</p>
            </div>
            
            <div x-show="currentStep === 1">
                <h3>Step 2: Content</h3>
                <p>This is the content step.</p>
            </div>
            
            <div x-show="currentStep === 2">
                <h3>Step 3: Pricing</h3>
                <p>This is the pricing step.</p>
            </div>
            
            <div x-show="currentStep === 3">
                <h3>Step 4: SEO</h3>
                <p>This is the SEO step.</p>
            </div>
        </div>
    </div>
</body>
</html>
