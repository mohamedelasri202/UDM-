<?php 
require_once 'classes/connection.php';
class Tags {
    private $id;
    private $title;

    public function __construct($id, $title) {
        $this->id = $id;
        $this->title = $title;
    }

    public function getID() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function addTags() {
        $db = Database::getInstance()->getConnection();
        $sql = $db->prepare("INSERT INTO tags (title) VALUES (:title)");
        $sql->bindParam(':title', $this->title);
        
        if($sql->execute()) {
            $this->id = $db->lastInsertId();
            return new Tags($this->id, $this->title);
        }
        return null;
    }

    public static function afficheTags() {
        $db = Database::getInstance()->getConnection();
        $stm = $db->prepare("SELECT * FROM tags");
        $stm->execute();
        
        $tags = [];
        $results = $stm->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($results as $row) {
            $tags[] = new Tags($row['id'], $row['title']);
        }
        
        return $tags;
    }

    public function editeTags() {
        $db = Database::getInstance()->getConnection();
        $stm = $db->prepare("UPDATE tags SET title = :title WHERE id = :id");
        $stm->bindParam(':title', $this->title);
        $stm->bindParam(':id', $this->id);
        
        if($stm->execute()) {
            return new Tags($this->id, $this->title);
        }
        return null;
    }

    public function deleteTags() {
        $db = Database::getInstance()->getConnection();
        $stm = $db->prepare("DELETE FROM tags WHERE id = :id");
        $stm->bindParam(':id', $this->id);
        
        return $stm->execute();
    }

    public static function getTagById($id) {
        $db = Database::getInstance()->getConnection();
        $stm = $db->prepare("SELECT * FROM tags WHERE id = :id");
        $stm->bindParam(':id', $id);
        $stm->execute();
        
        if($row = $stm->fetch(PDO::FETCH_ASSOC)) {
            return new Tags($row['id'], $row['title']);
        }
        return null;
    }
}







// In your form processing file (e.g., process_form.php)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the comma-separated tags from the form
    $tagTitles = $_POST['tag_title']; 

    if (!empty($tagTitles)) {
        // Split the tags into an array
        $tagsArray = array_map('trim', explode(',', $tagTitles));

        foreach ($tagsArray as $tagTitle) {
            if (!empty($tagTitle)) {
                // Create a new Tags object for each tag
                $tag = new Tags(null, $tagTitle);
                $newTag = $tag->addTags();

                if ($newTag) {
                    echo "Tag added successfully with ID: " . $newTag->getID() . "<br>";
                } else {
                    echo "Error adding tag: $tagTitle<br>";
                }
            }
        }
    } else {
        echo "No tags provided.";
    }
}












?>


<style>
.tag-input-container {
    margin-top: 5px;
}

.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 10px;
}

.tag {
    background-color: #e9ecef;
    border-radius: 3px;
    padding: 5px 10px;
    display: inline-flex;
    align-items: center;
}

.tag span {
    margin-right: 5px;
}

.tag button {
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 0 5px;
}

.tag button:hover {
    color: #dc3545;
}
</style>

<form method="POST">
    <div class="form-group">
        <label for="tagInput">Course Tags</label>
        <div class="tag-input-container">
            <input type="text" id="tagInput" placeholder="Enter tags and press Enter">
            <div id="tagContainer" class="tags-container"></div>
            <!-- Hidden input to store tags for form submission -->
            <input type="hidden" name="tag_title" id="tag_title">
        </div>
    </div>
    <button type="submit">Add Tag</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tagInput = document.getElementById('tagInput');
    const tagContainer = document.getElementById('tagContainer');
    const hiddenTagInput = document.getElementById('tag_title');
    let tags = [];

    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tag = this.value.trim();

            if (tag && !tags.includes(tag)) {
                tags.push(tag);
                updateTags();
                this.value = '';
            }
        }
    });

    function updateTags() {
        // Update the hidden input with a comma-separated string of tags
        hiddenTagInput.value = tags.join(',');

        // Update the visual representation of tags
        tagContainer.innerHTML = tags.map(tag => `
            <div class="tag">
                <span>${escapeHtml(tag)}</span>
                <button type="button" onclick="removeTag('${escapeHtml(tag)}')">&times;</button>
            </div>
        `).join('');
    }

    // Remove a tag from the list
    window.removeTag = function(tagToRemove) {
        tags = tags.filter(tag => tag !== tagToRemove);
        updateTags();
    }

    // Escape HTML to prevent potential XSS
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});


</script>
