<footer class="py-4 bg-white mt-auto border-top">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">
                &copy; <?php echo date('Y'); ?> Student Forum. All rights reserved.
            </div>
            <div>
                <a href="#" class="text-muted">Privacy Policy</a>
                &middot;
                <a href="#" class="text-muted">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>

<!-- Core JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom scripts -->
<script>
    // Add fade-in animation to alerts
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.add('fade-in');
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(() => {
                $(alert).alert('close');
            }, 10000);
        });
        
        // Activate tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

</body>
</html> 