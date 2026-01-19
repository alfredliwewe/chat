<script src="tailwind.js"></script>
<!--jquery-->
<script src="../resources/vendor/jquery/jquery.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Outfit', sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    }
    /* Floating Label Fix */
    .input-group label {
        transition: all 0.2s ease-out;
        pointer-events: none;
        z-index: 10;
    }
    .input-group input:focus ~ label,
    .input-group input:not(:placeholder-shown) ~ label {
         transform: translateY(-26px) scale(0.85); /* Adjusted translation */
        color: #667eea;
        background-color: white; /* Mask the border */
        padding: 0 6px;
        margin-left: -6px;
    }
</style>