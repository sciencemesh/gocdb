<!DOCTYPE html>
<html>
<head>
	<script>
		const STATE_NONE = 0
		const STATE_STATUS = 1
		const STATE_SUCCESS = 2
		const STATE_ERROR = 3

		function enableForm(id, enable) {
			var form = document.getElementById(id);
			var elements = form.elements;
			for (var i = 0, len = elements.length; i < len; ++i) {
				elements[i].disabled = !enable;
			}
		}

		function setElementVisibility(id, visible) {
			var elem = document.getElementById(id);
			if (visible) {
				elem.classList.add("visible");
				elem.classList.remove("hidden");
			} else {
				elem.classList.remove("visible");
				elem.classList.add("hidden");
			}
		}

		function setState(state, msg = "", formId = null, focusElem = null, formState = null) {
			setElementVisibility("status", state == STATE_STATUS);
			setElementVisibility("success", state == STATE_SUCCESS);
			setElementVisibility("error", state == STATE_ERROR);

			var elem = null;
			switch (state) {
			case STATE_STATUS:
				elem = document.getElementById("status");
				break;

			case STATE_SUCCESS:
				elem = document.getElementById("success");
				break;

			case STATE_ERROR:
				elem = document.getElementById("error");
				break;
			}

			if (elem !== null) {
				elem.innerHTML = msg;
			}

			if (formId !== null && formState !== null) {
				enableForm(formId, formState);
			}

			if (focusElem !== null) {
				var elem = document.getElementById(focusElem);
				elem.focus();
			}
		}

        function verifyForm(formData) {
        	if (formData.get("token") == "") {
        		setState(STATE_ERROR, "Please enter your token.", "form", "token", true);
        		return false;
        	}

        	return true;
        }

        function handleAction() {
        	const formData = new FormData(document.querySelector("form"));
        	if (!verifyForm(formData)) {
        		return;
        	}

        	setState(STATE_STATUS, "Verifying token... this should only take a moment.", "form", null, false);

        	var xhr = new XMLHttpRequest();
            xhr.open("GET", "<?php echo getenv('SITEACC_API') . '/verify-user-token?token='; ?>" + formData.get("token"));
            xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');

        	xhr.onreadystatechange = function() {
        		if (this.readyState === XMLHttpRequest.DONE) {
					var resp = JSON.parse(this.responseText);

        			if (this.status == 200) {
						setState(STATE_SUCCESS, "Your token is valid!");
        			} else {
        				setState(STATE_ERROR, "Your token is invalid:<br><em>" + resp.error + "</em>", "form", null, true);
        			}
                }
        	}

            xhr.send();
        }
	</script>

	<style>
		form {
			border-color: lightgray !important;
		}
		button {
			min-width: 140px;
		}
		input {
			width: 95%;
		}
		label {
			font-weight: bold;
		}

		.box {
			width: 100%;
			border: 1px solid black;
			border-radius: 10px;
			padding: 10px;
		}
		.container {
			width: 900px;
			display: grid;
			grid-gap: 10px;
			position: absolute;
			left: 50%;
			transform: translate(-50%, 0);
		}
		.container-inline {
			display: inline-grid;
			grid-gap: 10px;
		}
		.status {
			border-color: #F7B22A;
			background: #FFEABF;
		}
		.success {
			border-color: #3CAC3A;
			background: #D3EFD2;
		}
		.error {
			border-color: #F20000;
			background: #F4D0D0;
		}
		.visible {
			display: block;
		}
		.hidden {
			display: none;
		}

		html * {
			font-family: arial !important;
		}

		.mandatory {
			color: red;
			font-weight: bold;
		}
	</style>
	<title>ScienceMesh Account Login</title>
</head>
<body>

<div class="container">
	<div>
		<form id="form" method="POST" class="box container-inline" style="width: 100%;">
			<div style="grid-row: 1;"><label for="token">Token: <span class="mandatory">*</span></label></div>
			<div style="grid-row: 2; grid-column: 1 / span 2;"><input type="text" id="token" name="token"/></div>

			<div style="grid-row: 3; align-self: center;">
				Fields marked with <span class="mandatory">*</span> are mandatory.
			</div>
			<div style="grid-row: 3; grid-column: 2; text-align: right;">
				<button type="reset">Reset</button>
				<button type="button" style="font-weight: bold;" onClick="handleAction();">Verify</button>
			</div>
		</form>
	</div>
	<div>
		Go <a href="../">back</a> to the GOCDB front page.
	</div>

	<div id="status" class="box status hidden"></div>
	<div id="success" class="box success hidden"></div>
	<div id="error" class="box error hidden"></div>
</div>
</body>
</html>
