<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            color: #1a1a1a;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .hero-section {
            position: relative;
            height: 400px;
            width: 100%;
            background-color: #1a1a1a;
            overflow: hidden;
        }

        .hero-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.8;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .course-header {
            background-color: white;
            padding: 3rem 0;
            border-bottom: 1px solid #eaeaea;
            margin-bottom: 2rem;
            margin-top: -100px;
            position: relative;
            z-index: 2;
            border-radius: 1rem 1rem 0 0;
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.05);
        }

        .course-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .course-meta {
            display: flex;
            gap: 2rem;
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tags-container {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .tag {
            background-color: #f3f4f6;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            color: #4b5563;
        }

        .course-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .main-content {
            background: white;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .sidebar {
            background: white;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #111;
        }

        .description {
            color: #4b5563;
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .enroll-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            text-align: center;
        }

        .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #111;
            margin-bottom: 1rem;
        }

        .enroll-button {
            width: 100%;
            padding: 1rem;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .enroll-button:hover {
            background-color: #4338ca;
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eaeaea;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111;
        }

        .stat-label {
            color: #666;
            font-size: 0.875rem;
        }

        .categories {
            margin-top: 2rem;
        }

        .category-tag {
            display: inline-block;
            margin: 0.25rem;
            padding: 0.5rem 1rem;
            background-color: #f3f4f6;
            border-radius: 0.5rem;
            color: #4b5563;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .course-content {
                grid-template-columns: 1fr;
            }

            .hero-section {
                height: 300px;
            }

            .course-header {
                margin-top: -50px;
            }

            .course-meta {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="hero-section">
        <img src="/api/placeholder/1200/400" alt="Course Cover Image" class="hero-image">
    </div>

    <div class="course-header">
        <div class="container">
            <h1 class="course-title">Advanced Web Development with React</h1>
            <div class="course-meta">
                <div class="meta-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Created by John Doe
                </div>
                <div class="meta-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Last updated April 2024
                </div>
            </div>
            <div class="tags-container">
                <span class="tag">React</span>
                <span class="tag">JavaScript</span>
                <span class="tag">Web Development</span>
                <span class="tag">Frontend</span>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="course-content">
            <div class="main-content">
                <h2 class="section-title">Course Description</h2>
                <p class="description">
                    Master modern web development with React through this comprehensive course. You'll learn everything from fundamental concepts to advanced patterns, state management, and best practices for building scalable applications. This course includes hands-on projects and real-world examples to help you gain practical experience.
                </p>

                <h2 class="section-title">What You'll Learn</h2>
                <p class="description">
                    • React fundamentals and advanced concepts<br>
                    • State management with Redux and Context API<br>
                    • Modern React patterns and best practices<br>
                    • Performance optimization techniques<br>
                    • Building responsive and accessible interfaces<br>
                    • Testing and debugging React applications
                </p>
            </div>

            <div class="sidebar">
                <div class="enroll-card">
                    <div class="price">$99.99</div>
                    <button class="enroll-button">Enroll Now</button>

                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-value">2,456</div>
                            <div class="stat-label">Students</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">4.8</div>
                            <div class="stat-label">Rating</div>
                        </div>
                    </div>
                </div>

                <div class="categories">
                    <h3 class="section-title">Categories</h3>
                    <div>
                        <span class="category-tag">Development</span>
                        <span class="category-tag">Web Design</span>
                        <span class="category-tag">Programming</span>
                        <span class="category-tag">Frontend</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>