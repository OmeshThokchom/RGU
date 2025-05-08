<?php
function renderActionButtons($id = '') {
    ?>
    <div class="action-buttons">
        <button class="btn-glass" data-action="edit" data-id="<?php echo $id; ?>">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            <span>Edit</span>
        </button>
        <button class="btn-glass" data-action="delete" data-id="<?php echo $id; ?>">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>Delete</span>
        </button>
    </div>
    <?php
}

function renderAddButton($text = 'Add New') {
    ?>
    <button class="fab-glass glass" data-action="add">
        <svg class="icon" viewBox="0 0 24 24">
            <path d="M12 5v14m-7-7h14"/>
        </svg>
    </button>
    <?php
}
?>