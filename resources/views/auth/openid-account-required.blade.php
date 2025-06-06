@extends('templates.wrapper', [
    'css' => ['body' => 'bg-black']
])

@section('title')
    Account Required
@endsection

@section('container')
    <div class="min-h-screen bg-black flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-12 w-12 text-orange-500">
                    <svg class="h-full w-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 14.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-white">Account Required</h2>
                <p class="mt-2 text-sm text-zinc-400">
                    You need to purchase a server to access this panel
                </p>
            </div>
            
            <div class="bg-zinc-900 border border-zinc-700 shadow-2xl rounded-xl p-6">
                <div class="bg-orange-900/20 border border-orange-600/30 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-orange-200">No Account Found</h3>
                            <div class="mt-2 text-sm text-orange-300/80">
                                <p>You have successfully authenticated with your OpenID provider, but you don't have an account on this server management panel.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-white">To access this panel, you need to:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-zinc-300">
                        <li><strong class="text-white">Purchase a server</strong> from our hosting service</li>
                        <li>Your account will be automatically created when you make your first purchase</li>
                        <li>Once your account exists, you can use OpenID to log in</li>
                    </ol>
                    
                    <div class="bg-blue-900/20 border border-blue-600/30 rounded-lg p-4 mt-6">
                        <h5 class="flex items-center text-sm font-medium text-blue-200">
                            <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Why do I need to purchase a server?
                        </h5>
                        <p class="mt-2 text-sm text-blue-300/80">
                            This panel is exclusively for managing servers that you've purchased from our hosting service. 
                            Without an active server or account, there's nothing to manage here.
                        </p>
                    </div>
                </div>
                
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="/" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-4 rounded-lg text-sm font-medium transition-colors duration-200 border-0">
                        <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Browse Server Plans
                    </a>
                    <a href="/auth/login?bypass_redirect=true" class="flex-1 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 text-center py-3 px-4 rounded-lg text-sm font-medium transition-colors duration-200 border-0">
                        <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Try Different Login Method
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
