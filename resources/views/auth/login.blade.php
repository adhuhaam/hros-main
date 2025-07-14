<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HR Management System</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    
    <style>
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        .content-overlay {
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-blue-900 text-white min-h-screen flex flex-col justify-center items-center relative overflow-hidden">
    
    <div id="particles-js"></div>
    
    <div class="content-overlay w-full px-4 py-10 max-w-7xl">
        <div class="flex flex-col items-center">
            <img src="/assets/images/logos/dark-logo.svg" class="w-60 mb-6" alt="Logo" />
            
            <div class="bg-white text-gray-800 rounded-lg shadow-lg p-8 w-full max-w-md">
                <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
                
                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username
                        </label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>
                    
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="remember" 
                                   name="remember" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                        
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                            Forgot password?
                        </a>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        Sign In
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="#" class="text-blue-600 hover:text-blue-500">Contact HR Department</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { 
                    value: 60, 
                    density: { 
                        enable: true, 
                        value_area: 800 
                    } 
                },
                color: { 
                    value: "#ffffff" 
                },
                shape: { 
                    type: "circle", 
                    stroke: { 
                        width: 0, 
                        color: "#000000" 
                    } 
                },
                opacity: { 
                    value: 0.5 
                },
                size: { 
                    value: 6, 
                    random: true 
                },
                line_linked: { 
                    enable: true, 
                    distance: 150, 
                    color: "#ffffff", 
                    opacity: 0.4, 
                    width: 1 
                },
                move: { 
                    enable: true, 
                    speed: 3 
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: { 
                    onhover: { 
                        enable: true, 
                        mode: "grab" 
                    }, 
                    onclick: { 
                        enable: true, 
                        mode: "push" 
                    } 
                },
                modes: {
                    grab: { 
                        distance: 140, 
                        line_linked: { 
                            opacity: 1 
                        } 
                    },
                    push: { 
                        particles_nb: 4 
                    }
                }
            },
            retina_detect: true
        });
    </script>
</body>
</html>