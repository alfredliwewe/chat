<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#085a9d">
<link rel="icon" type="image/png" href="../uploads/<?=$config['favicon'];?>">
<link rel="stylesheet" type="text/css" href="../../resources/w3css/w3.css">
<link rel="stylesheet" type="text/css" href="../../resources/w3css/tailwind.css">
<link rel="stylesheet" type="text/css" href="../w3css/w3-theme-indigo.css">
<link href="../../resources/vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="../assets/css/custom-style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../assets/css/style.css">
<script src="../tailwind.js"></script>


<script type="text/javascript" src="../../resources/vendor/jquery/jquery.min.js"></script>
<link rel="stylesheet" href="../../resources/fontawesome/css/all.min.css">
<script type="text/javascript" src="../dataTable.js"></script>

<script src="../dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="../dist/sweetalert2.min.css">
<!--====== Default CSS ======-->
<link rel="stylesheet" href="../../resources/w3css/default.css">


<!--====== Style CSS ======-->
<link rel="stylesheet" href="../assets/css/style.css">

<!--====== Nice Select CSS ======-->
<link rel="stylesheet" href="../assets/css/nice-select.css">
<!--====== Nice Select js ======-->
<script src="../assets/js/jquery.nice-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../resources/toastify/src/toastify.css">
<script type="text/javascript" src="../../resources/toastify/src/toastify.js"></script>

<link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="../../../resources/semantic/semantic.min.css">

<!--<script type="text/javascript" src="exportTable.js"></script>-->
<script type="text/javascript" src="../../resources/react.js"></script>
<script type="text/javascript" src="../../resources/react-dom.js"></script>
<script type="text/javascript" src="../../resources/babel.js"></script>
<script type="text/javascript" src="../../resources/prop-types.js"></script>
<script type="text/javascript" src="../../resources/moment.js"></script>

<script type="text/javascript" src="../../resources/react-is.js"></script>
<link rel="stylesheet" href="../assets/style.css">
<script type="text/javascript" src="../../resources/material-ui.js"></script>
<script type="text/javascript" src="../nicEdit.js"></script>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=home" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=home" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<style>
    .material-symbols-outlined {
        font-variation-settings:
        'FILL' 0,
        'wght' 400,
        'GRAD' 0,
        'opsz' 24
    }
    .transparent-hover:hover{
        background: rgba(0, 0, 0, .040) !important;
    }
</style>


<style type="text/css">
	@font-face{
		font-family: googleRoboto;
		src:url('../fonts/Roboto/Roboto-Regular.ttf');
	}
	@font-face{
		font-family: robotLight;
		src:url('../fonts/Roboto/Roboto-Light.ttf');
	}
	@font-face{
		font-family: openSans;
		src:url('../fonts/Open_Sans/OpenSans-Regular.ttf');
	}
	@font-face{
		font-family: sourceSans;
		src:url('../fonts/Source_Sans_Pro/SourceSansPro-Regular.ttf');
	}
	body{
		font-family: Arial, Helvetica, sans-serif;
	}
	.w3-grey{
		background: #9eb1bb !important;
	}
	.tp.w3-grey{
		border-left: 3px solid red ;
	}
	.tp{

	}
	.block{
		display: block;
	}
	.bold{
		font-weight: bold;
	}
	thead{
		border-top-left-radius: 8px !important;
		border-top-right-radius: 8px !important;
		cursor: pointer;
	}
	.btn.btn-sm{
		padding: 5px 22px;
		border-radius: 64px;
		font-size: 1.0rem;
		cursor: pointer;
	}
	.form-control.sw{
		padding-left: 40px !important;min-height: 47px !important;
	}
	.pointer{
		cursor: pointer;
	}
	.rounded-left{
		border-radius: 0 !important;
		border-bottom-left-radius: 6px !important;
		border-top-left-radius: 6px !important;
	}
	.rounded-right{
		border-radius: 0 !important;
		border-bottom-right-radius: 26px !important;
		border-top-right-radius: 26px !important;
	}
	.bcenter{
        display: inline-flex;
        flex-flow: column nowrap;
        justify-content: center;
        align-items: center;
    }
    .btn-succes,.some-padding:hover{
    	background: #023e8a;
    	color: white;
    	cursor: pointer;
    }
    .some-padding{
        padding:12px 16px !important
    }
    .w3-table td{
    	font-family: "Roboto","Helvetica","Arial",sans-serif;
		font-weight: 400;
		font-size: 0.875rem;
		line-height: 1.43;
		letter-spacing: 0.01071em;
		display: table-cell;
		vertical-align: inherit;
		border-bottom: 1px solid rgba(224, 224, 224, 1);
		text-align: left;
		color: rgba(0, 0, 0, 0.87);
    }
</style>
<script type="text/javascript">
	function _(id) {
        return document.getElementById(id);
    }

    function Toast(text) {
        Toastify({
            text: text,
            gravity: "top",
            position: 'center',
            backgroundColor:"#dc3545",
            background:"#01796f"
        }).showToast();
    }
</script>