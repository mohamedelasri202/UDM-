<form method="POST" enctype="multipart/form-data" class="space-y-6">
    <div class="form-group">
        <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
        <input type="text" name="title" id="title" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div class="form-group">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="4" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div>

    <div class="form-group">
        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
        <input type="number" name="price" id="price" step="0.01" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div class="form-group">
        <label for="id_categorie" class="block text-sm font-medium text-gray-700">Category</label>
        <select name="id_categorie" id="id_categorie" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['id']); ?>">
                    <?php echo htmlspecialchars($category['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Course Type Selection -->
    <div class="form-group">
        <label for="coursetype" class="block text-sm font-medium text-gray-700">Course Type</label>
        <select id="coursetype" name="coursetype" onchange="toggleCourseInputs()" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select course type</option>
            <option value="text">Text Course</option>
            <option value="video">Video Course</option>
        </select>
    </div>

    <!-- Text Course Content -->
    <div id="textCourseContent" class="form-group" style="display: none;">
        <label for="documentcourse" class="block text-sm font-medium text-gray-700">Course Content (Text)</label>
        <textarea id="documentcourse" name="documentcourse" rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div>

    <!-- Video Course Content -->
    <div id="videoCourseContent" class="form-group" style="display: none;">
        <label for="videocourse" class="block text-sm font-medium text-gray-700">Course Video</label>
        <input type="file" name="videocourse" id="videocourse" accept="video/*"
            class="mt-1 block w-full text-sm text-gray-500
            file:mr-4 file:py-2 file:px-4
            file:rounded-md file:border-0
            file:text-sm file:font-semibold
            file:bg-indigo-50 file:text-indigo-700
            hover:file:bg-indigo-100">
        <p class="mt-1 text-sm text-gray-500">Upload your course video file (MP4, WebM, etc.)</p>
    </div>

    <!-- Course Thumbnail -->
    <div class="form-group">
        <label for="coursimage" class="block text-sm font-medium text-gray-700">Course Thumbnail</label>
        <input type="file" name="coursimage" id="coursimage" accept="image/*" required
            class="mt-1 block w-full text-sm text-gray-500
            file:mr-4 file:py-2 file:px-4
            file:rounded-md file:border-0
            file:text-sm file:font-semibold
            file:bg-indigo-50 file:text-indigo-700
            hover:file:bg-indigo-100">
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Add Course
        </button>
    </div>
</form>

<!-- JavaScript for Dynamic Input -->
<script>
    function toggleCourseInputs() {
        const courseType = document.getElementById('coursetype').value;
        const textContent = document.getElementById('textCourseContent');
        const videoContent = document.getElementById('videoCourseContent');

        // Hide both by default
        textContent.style.display = 'none';
        videoContent.style.display = 'none';

        // Show the appropriate input based on the selected type
        if (courseType === 'text') {
            textContent.style.display = 'block';
        } else if (courseType === 'video') {
            videoContent.style.display = 'block';
        }
    }
</script>
