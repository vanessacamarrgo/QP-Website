// dados virão do HTML
var allNames = window.allNames || [];

var filterInput  = document.getElementById('filter-name');
var autocompleteList  = document.getElementById('autocompleteList');
var highlightedIndex = -1;

filterInput.addEventListener('input', function () {
    var typed = this.value.trim().toLowerCase();
    autocompleteList.innerHTML = '';
    highlightedIndex = -1;

    if (typed.length === 0) {
        autocompleteList.style.display = 'none';
        return;
    }

    var matches = allNames.filter(function (name) {
        return name.toLowerCase().includes(typed);
    });

    if (matches.length === 0) {
        autocompleteList.style.display = 'none';
        return;
    }

    matches.forEach(function (name, index) {
        var item = document.createElement('div');
        item.className = 'autocomplete-item';
        item.textContent = name;

        item.addEventListener('mousedown', function () {
            filterInput.value = name;
            autocompleteList.style.display = 'none';
        });

        autocompleteList.appendChild(item);
    });

    autocompleteList.style.display = 'block';
});

filterInput.addEventListener('keydown', function (e) {
    var items = autocompleteList.querySelectorAll('.autocomplete-item');

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        highlightedIndex = (highlightedIndex + 1) % items.length;
        updateHighlight(items);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        highlightedIndex = (highlightedIndex - 1 + items.length) % items.length;
        updateHighlight(items);
    } else if (e.key === 'Enter' && highlightedIndex >= 0) {
        e.preventDefault();
        filterInput.value = items[highlightedIndex].textContent;
        autocompleteList.style.display = 'none';
        highlightedIndex = -1;
    } else if (e.key === 'Escape') {
        autocompleteList.style.display = 'none';
        highlightedIndex = -1;
    }
});

function updateHighlight(items) {
    items.forEach(function (item, i) {
        item.classList.toggle('highlighted', i === highlightedIndex);
    });
}

document.addEventListener('click', function (e) {
    if (!filterInput.contains(e.target) && !autocompleteList.contains(e.target)) {
        autocompleteList.style.display = 'none';
    }
});

// MODAL
function openDeleteModal(deleteUrl) {
    document.getElementById('deleteConfirmLink').href = deleteUrl;
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}

document.getElementById('deleteModal').addEventListener('click', function (e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});