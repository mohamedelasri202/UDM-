<style>
    /* Fix for modal positioning and scrolling */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        overflow-y: auto;
        padding: 20px;
        box-sizing: border-box;
    }

    .modal-content {
        background-color: #fff;
        margin: 2rem auto;
        padding: 2rem;
        border-radius: 8px;
        width: 90%;
        max-width: 600px;
        position: relative;
        max-height: calc(100vh - 4rem);
        overflow-y: auto;
    }

    /* Better form styling */
    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        background-color: #fff;
        box-sizing: border-box;
    }

    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    /* Fix file input styling */





    .form-group input[type="file"] {
        padding: 0.5rem 0;
        width: 100%;
    }

    /* Better button styling */
    .submit-btn {
        background-color: #4CAF50;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.2s ease;
    }

    .submit-btn:hover {
        background-color: #45a049;
    }

    .close-btn {
        position: absolute;
        right: 1rem;
        top: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
        color: #666;
        transition: color 0.2s ease;
    }

    .close-btn:hover {
        color: #000;
    }

    /* Fix modal header */
    .modal-header {
        padding-right: 2rem;
        margin-bottom: 2rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .modal-content {
            margin: 1rem;
            padding: 1rem;
            max-height: calc(100vh - 2rem);
        }

        .form-group {
            margin-bottom: 1rem;
        }
    }
</style>


































<div id="courseModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Course</h2>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Course Title</label>
                <input type="text" name="title" id="title" required>
            </div>

            <div class="form-group">
                <label for="tag_titles">Tags (comma-separated)</label>
                <input type="text" class="form-control" id="tag_titles" name="tag_titles" placeholder="Enter tags separated by commas">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="id_categorie">Category</label>
                <select name="id_categorie" id="id_categorie" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>">
                            <?php echo htmlspecialchars($category['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="coursetype">Course Type</label>
                <select id="coursetype" name="coursetype" onchange="toggleCourseInputs()" required>
                    <option value="">Select course type</option>
                    <option value="text">Text Course</option>
                    <option value="video">Video Course</option>
                </select>
            </div>

            <div id="textCourseContent" class="form-group" style="display: none;">
                <label for="documentcourse">Course Content (Text)</label>
                <textarea id="documentcourse" name="documentcourse" rows="4"></textarea>
            </div>

            <div id="videoCourseContent" class="form-group" style="display: none;">
                <label for="videocourse">Course Video</label>
                <input type="file" name="videocourse" id="videocourse" accept="video/*">
                <p class="help-text">Upload your course video file (MP4, WebM, or Ogg)</p>
            </div>

            <div class="form-group">
                <label for="coursimage">Course Thumbnail</label>
                <input type="file" name="coursimage" id="coursimage" accept="image/*" required>
                <p class="help-text">Upload a course thumbnail image (JPEG, PNG, or GIF)</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Add Course</button>
            </div>
        </form>
    </div>
</div>