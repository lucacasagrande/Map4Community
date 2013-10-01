<!DOCTYPE html>
<?php
include("settings.php");
?>
<html lang="en">
<head>
    <title><?php echo $TITLE ?></title>

    <link rel="stylesheet" href="lib/leaflet-0.6.4/leaflet.css" />
    <!--[if lte IE 8]><link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.2/leaflet.ie.css" /><![endif]-->
    <script src="lib/leaflet-0.6.4/leaflet.js"></script>

    <link rel="stylesheet" href="lib/leaflet-0.6.4/plugins/MarkerCluster.css" />
    <link rel="stylesheet" href="lib/leaflet-0.6.4/plugins/MarkerCluster.Default.css" />
    <!--[if lte IE 8]><link rel="stylesheet" href="../dist/MarkerCluster.Default.ie.css" /><![endif]-->
    <script src="lib/leaflet-0.6.4/plugins/leaflet.markercluster-src.js"></script>
    <script src="lib/leaflet-0.6.4/plugins/leaflet.geocsv.js"></script>
    <script src="lib/jquery-1.10.2.min.js"></script>
    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <style>
      .right {
	  text-align: right;
      }
      h2 {
	  text-align: center;
      }
      html, body {
	  margin: 0;
	  padding: 0;
	  height: 100%;
	  width: 100%;
      }
      #map {
	  margin-top:40px;
	  width:100%;
	  height:100%;
      }
      .leaflet-container {
	  font: 16px/1.5 "Helvetica Neue", Arial, Helvetica, sans-serif;
      }
      .navbar, .navbar-fixed-top, .navbar-default {
	  background-color: #FFFFFF!important;
	  background-image: linear-gradient(to bottom,#FFFFFF,#FFFFFF)!important;
      }
      .navbar-header {
	  padding-left: 10px;
	  padding-top: 5px;
      }
      .pull-right {
	  padding-right: 10px;
      }
      #toolbar {
	  padding-right: 10px;
      }
      .btn-primary {
	  background-image: linear-gradient(to bottom,#03FF54,#009718);
	  background-color: #009718;
      }
      .btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary:hover, .btn-primary.active, .btn-primary.disabled, .btn-primary[disabled] {
	  background-color: #009718!important;
      }
    </style>
</head>
<body>
<!--     <h2>User map using leaflet, jquery and bootstrap</h2> -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <ul class="nav navbar-nav navbar-header">
	<h4><?php echo $TITLE ?></h4>
      </ul>
        <ul class="nav navbar-nav navbar-text pull-right">
	    <button type="submit" class="btn btn-primary right" id="navigate">
		Navigate
	    </button>
	    <button type="submit" class="btn btn-primary right" id="useradd">
		<i class="icon-user icon-white"></i>Add your self
	    </button>
	    <button type="submit" class="btn btn-primary right" id="zoommax">
		<span class="glyphicon glyphicon-globe"></span>Zoom to max extent
	    </button>
	</ul>
      </div>
    </nav>
    <div class="modal hide fade" id="updateUser">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">x</a>
        <h3>Update user information</h3>
      </div>
      <div class="modal-body">
	  <form class="form-signin"  action="">
	      <input type="text" class="input-block-level" id="updateName" placeholder="Name and surname">
	      <input type="password" class="input-block-level" id="updatePassword" placeholder="Password">
	      <input type="text" class="input-block-level" id="updateCompany" placeholder="Company">
	      <input type="text" class="input-block-level" id="updateWebsite" placeholder="Website (with http://)">
	      <button type="button" class="btn btn-primary right" onclick="updateUser()">Update</button>
	  </form>
      </div>
    </div>
    <div class="modal hide fade" id="removeUser">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">x</a>
        <h3>Remove user</h3>
      </div>
      <div class="modal-body">
	  <form class="form-signin"  action="">
	      <input type="text" class="input-block-level" id="removeName" placeholder="Name and surname">
	      <input type="password" class="input-block-level" id="removePassword" placeholder="Password">
	      <button type="button" class="btn btn-primary right" onclick="removeUser()">Remove</button>
	  </form>
      </div>
    </div>
    <div class="modal hide fade" id="updateSuccess">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">x</a>
        <h3>Success</h3>
      </div>
      <div class="modal-body">
	  The update run successfully <br />
	  <button type="button" class="btn btn-primary right" onclick="$('#updateSuccess').modal('hide');">Ok</button>
      </div>
    </div>
    <div class="modal hide fade" id="removeSuccess">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">x</a>
        <h3>Success</h3>
      </div>
      <div class="modal-body">
	  The deletion run successfully <br />
	  <button type="button" class="btn btn-primary right" onclick="$('#removeSuccess').modal('hide');">Ok</button>
      </div>
    </div>
    <div class="modal hide fade" id="errorName">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">x</a>
        <h3>Error</h3>
      </div>
      <div class="modal-body">Name is required!</div>
    </div>
    <div class="modal hide fade" id="errorPasswd">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">x</a>
        <h3>Error</h3>
      </div>
      <div class="modal-body">Password is required!</div>
    </div>
    <div class="modal hide fade" id="errorUrl">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">x</a>
        <h3>Error</h3>
      </div>
      <div class="modal-body">The url seems to be wrong!<br />Please insert a valid url with <i>http://</i></div>
    </div>
    <div id="map"></div>
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>
    <script>
        var user, popup, cluster, updateString;
        var csvfile = "<?php Print($CSVNAME); ?>";
	function insertUser() {
	    var name = $("#name").val();
	    var comp = $("#company").val();
	    var slat = $("#lat").val();
	    var slon = $("#lon").val();
	    var web = $("#website").val();
	    var passwd = $("#passwd").val();
	    if (name.length == 0) {
	      $("#errorName").modal('show');
	      return false;
	    }
	    if (passwd.length == 0) {
	      $("#errorPasswd").modal('show');
	      return false;
	    }
	    if (web && web.indexOf('http://') != 0){
	      $("#errorUrl").modal('show');
	      return false
	    }
	    var dataString = 'name='+ name + '&company=' + comp + '&website=' + web + '&lat=' + slat + '&lon=' + slon + '&passwd=' + passwd;
	    map.closePopup()
	    $.ajax({
	      type: "POST",
	      url: "add_user.php",
	      data: dataString,
	      success: function() {
		$("#updateUser").modal('hide');
		$("#updateSuccess").modal('show');
		user.clearLayers();
		cluster.clearLayers();
		getUser();
	      },
	      error: function(request, status, error) {
		console.log(request.responseText);
	      }
	    });
	    return false;
	}
	function removeUserForm(u){
	    $("#removeName")[0].value = u;
	    $("#removeUser").modal('show');
	    return false;
	}
	function removeUser(){
	    var name = $("#removeName").val();
	    var passwd = $("#removePassword").val();
	    var removeString = '&name=' + name + '&passwd=' + passwd;
	    console.log(removeString);
	    $.ajax({
	      type: "POST",
	      url: "remove_user.php",
	      data: removeString,
	      success: function() {
		$("#removeUser").modal('hide');
		$("#removeSuccess").modal('show');
		user.clearLayers();
		cluster.clearLayers();
		getUser();
	      },
	      error: function(request, status, error) {
		console.log(request.responseText);
	      }
	    });
	}
	
	function updateUserForm(u, c, w){
	    $('#updateName')[0].value = u;
	    if (c) {
		$('#updateCompany')[0].value = c;
	    } else {
		$('#updateCompany')[0].value = ''
	    }
	    if (w) {
		$('#updateWebsite')[0].value = w;
	    } else {
		$('#updateWebsite')[0].value = ''
	    }
	    $('#updateUser').modal('show');
	    updateString = 'oldname=' + u;
	    return false;
	}
	function updateUser(){
	    var name = $("#updateName").val();
	    var comp = $("#updateCompany").val();
	    var web = $("#updateWebsite").val();
	    var passwd = $("#updatePassword").val();
	    if (name.length == 0) {
	      $("#errorName").modal('show');
	      return false;
	    }
	    if (web && web.indexOf('http://') != 0){
	      $("#errorUrl").modal('show');
	      return false
	    }
	    updateString += '&name=' + name + '&company=' + comp + '&website=' + web + '&passwd=' + passwd;
	    console.log(updateString);
	    $.ajax({
	      type: "POST",
	      url: "update_user.php",
	      data: updateString,
	      success: function() {
		$("#updateUser").modal('hide');
		$("#updateSuccess").modal('show');
		user.clearLayers();
		cluster.clearLayers();
		getUser();
	      },
	      error: function(request, status, error) {
		console.log(request.responseText);
	      }
	    });
	}
	function getUser() {
	    user = L.geoCsv(null,{
		titles: ['lng', 'lat', 'User', 'Company', 'Website', 'Password'],
		fieldSeparator: ';',
		lineSeparator: '\n',
		deleteDobleQuotes: true,
		firstLineTitles: false,
		onEachFeature: function (feature, layer) {
		    var popup = '', oldUser = '', oldCompany = '', oldWebsite = '';
		    for (var clave in feature.properties) {
			var title = user.getPropertyTitle(clave);
			if (title == 'Company') {
			    if (feature.properties[clave]) {
				popup += '<b>'+title+'</b>: '+feature.properties[clave]+'<br />';
				oldCompany = feature.properties[clave];
			    } else {
				oldCompany = '';
			    }
			} else if (title == 'Website') {
			    if (feature.properties[clave]) {
				popup += '<b><a href="'+feature.properties[clave]+'" target="_blank">'+feature.properties[clave].replace('http://','')+'</a></b><br />';
				oldWebsite = feature.properties[clave];
			    } else {
				oldWebsite = '';
			    }
			} else if (title == 'Password') {
			    popup += '';
			} else {
			    popup += '<b>'+title+'</b>: '+feature.properties[clave]+'<br />';
			    oldUser = feature.properties[clave];
			}
		    }
		    popup += '<br /><button type="button" class="btn btn-primary btn-small" onclick="updateUserForm(\'' + oldUser + '\', \'' + oldCompany + '\',\'' + oldWebsite + '\')">Update user</button>'
		    popup += '<span style="padding-right: 10px;"></span><button type="button" class="btn btn-primary btn-small" onclick="removeUserForm(\'' + oldUser + '\')">Remove user</button>'
		    layer.bindPopup(popup);
		},
		pointToLayer: function (feature, latlng) {
		  return L.circleMarker(latlng, Style);
		}
	    });
	    $.ajax ({
		type:'GET',
		dataType:'text',
		url:csvfile,
		error: function() {
		    alert('No se pudieron cargar los datos');
		},
		success: function(csv) {
		    cluster = new L.MarkerClusterGroup();
		    user.addData(csv);
		    cluster.addLayer(user);
		    map.addLayer(cluster);
		    map.fitBounds(cluster.getBounds());
		},
	    });
	}
	var map = L.map('map').setView([0, 10], 2);
	var mapquestUrl = 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png';
	var subDomains = ['otile1','otile2','otile3','otile4'];
	var mapquestAttrib = 'Map information provided by <a href="http://open.mapquest.co.uk" target="_blank">MapQuest</a>,<a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> and contributors, created by Luca Delucchi.';
	var mapquest = new L.TileLayer(mapquestUrl, {maxZoom: 18, attribution: mapquestAttrib, subdomains: subDomains}).addTo(map);
	var Style = {
	    radius: 8,
	    fillColor: "#89D624",
	    color: "#65B200",
	    weight: 1,
	    opacity: 1,
	    fillOpacity: 0.8
	};
	getUser();
	popup = L.popup();

	function onMapClick(e) {
	    popup
		.setLatLng(e.latlng)
		.setContent('<form class="form-signin"  action=""> \
				<h4 class="form-signin-heading">Please add your self</h4> \
				<input type="text" class="input-block-level" id="name" placeholder="Name and surname"> \
				<input type="password" class="input-block-level" id="passwd" placeholder="Password"> \
				<input type="text" class="input-block-level" id="company" placeholder="Company"> \
				<input type="text" class="input-block-level" id="website" placeholder="Website (with http://)"> \
				<input type="hidden" class="input-block-level" id="lat" value="' + e.latlng.lat + '"> \
				<input type="hidden" class="input-block-level" id="lon" value="' + e.latlng.lng + '"> \
				<button type="button" class="btn btn-primary" onclick="insertUser()">Submit</button> \
			      </form>')
		.openOn(map);
	}

	$("#useradd").bind('click', function(event) {
	    $('#map').css('cursor', 'crosshair');
	    map.on('click', onMapClick);
	});
	$("#navigate").bind('click', function(event) {
	    $('#map').css('cursor', '');
	    map.off('click', onMapClick);
	});
	$("#zoommax").bind('click', function(event) {
	    map.closePopup()
	    map.fitBounds(cluster.getBounds());
	});
</script>

</body>
</html>