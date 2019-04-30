<?php require_once 'Scrapper.php'; ?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html lang="en">
    <head>
        <title>PHP Assessment Test</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
        <style>
            .w92{
                width: 92%; 
            }
            .header-primary{
                color: #fff;
                background-color: #337ab7;
                border-color: #2e6da4;
            }
            .navbar-default .navbar-brand {
                color: #FFF;
            }
            .navbar {
                border-radius: 0px;
            }
            .navbar-default .navbar-brand:focus, .navbar-default .navbar-brand:hover {
                color: #fff;
                background-color: transparent;
            }
            .alert-danger{
                display: none; 
            }
            img.img-responsive.loader {
                position: relative;
                left: 40%;
                width: 15%;
                display: none;
            }
            .product-image{
                width: 200px; 
            }
            .data-set{
                margin-top: 8%;
            }
        </style>
    </head>
    <body>

        <nav class="navbar navbar-default header-primary">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">PHP Assessment Test | Products Scrapper</a>
                </div>
                <div class="navbar-header pull-right">
                    <a class="navbar-brand" href="index.php">Sr. Software Engineer / PHP Developer | Abdulrehman Sheikh <small>(sheikhabdulrehman8@gmail.com)</small></a>
                </div>
            </div>
        </nav>
        <div class="container">
            <form action="#" method="post">
                <div class="alert alert-danger" ></div>
                <div class="form-group">
                    <input type="text" class="form-control pull-left w92" id="search_txt" placeholder="Search here...">
                    <button class="btn btn-default pull-right" id="search_btn" type="button" >Search</button>
                </div>
                <img src="loader.gif" class="img-responsive loader" />
            </form>
            <div class="row data-set">
                <?php 
                $obj = new Scrapper(""); 
                echo $obj->renderHTML(); 
                ?>
            </div>
        </div>
    </body>
</html>

<script>
    $("#search_btn").click(function () {
        if ($("#search_txt").val() == "")
        {
            $(".alert-danger").text("You have not entered a valid item to search.");
            $(".alert-danger").fadeIn(function () {
                setTimeout(function () {
                    $(".alert-danger").fadeOut();
                    $(".alert-danger").text("");
                }, 3000);
            });
        } else {
            $(".loader").show();
            $.post("Scrapper.php", {searchQry: $("#search_txt").val()}, function (response) {
                $(".loader").hide();
                $(".data-set").html(response);
            });
        }
    });
</script>
