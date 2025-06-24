
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Custom JS -->
<script src="../assets/js/script.js"></script>

<script>
// Ensure Bootstrap dropdowns work
$(document).ready(function() {
    // Initialize all Bootstrap dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
    
    // Debug dropdown clicks
    $('.dropdown-toggle').click(function(e) {
        console.log('Dropdown clicked');
        e.preventDefault();
        $(this).dropdown('toggle');
    });
});
</script>

</body>
</html>
