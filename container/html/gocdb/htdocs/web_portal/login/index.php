<!DOCTYPE html>
<html>
    <head>
    	<style>
    		html * {
    			font-family: arial !important;
    		}
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
    		.mandatory {
    			color: red;
    			font-weight: bold;
    		}
    	</style>
    	<title>ScienceMesh GOCDB Login</title>
    </head>

    <body>
        <div class="container">
        	<div><h1>Welcome to the ScienceMesh GOCDB login form!</h1></div>
        	<div>
        		<p>Fill out the form below to authenticate against the ScienceMesh GOCDB.</p>
        	</div>
        	<div>&nbsp;</div>
        	<div>
        		<form id="loginform" method="POST" action="login.php" class="box container-inline" style="width: 100%;">
        			<div style="grid-row: 1;"><label for="username">Username: <span class="mandatory">*</span></label></div>
        			<div style="grid-row: 2;"><input type="text" id="username" name="username"/></div>
        			<div style="grid-row: 3;"><label for="password">Password: <span class="mandatory">*</span></label></div>
        			<div style="grid-row: 4;"><input type="password" id="password" name="password"/></div>

        			<div style="grid-row: 5; align-self: center;">
        				Fields marked with <span class="mandatory">*</span> are mandatory.
        			</div>
        			<div style="grid-row: 5; grid-column: 2; text-align: right;">
        				<button type="reset">Reset</button>
        				<button type="submit" style="font-weight: bold;">Login</button>
        			</div>
        		</form>
        	</div>
        </div>
    </body>
</html>
