<html> 
    <head> 
        <title>sample page</title> 
        <script>
            console.log("hello world");
        </script>


    <style>
        h1 {
            color: blue;
        }
        p {
            color: red;
        }   
    </style>

    <button>click me</button>


    </head> 
    <body>
        <h1>hello world</h1> 
        <a href="https://www.youtube.com"> my fav site</a>
        <?php echo "test";
        $secret = "password123"; 
        $stack = [1, 2, 3]; 
        echo $stack[1]; 
        ?> 
        <h2>new line</h2> 
        <?php echo "new line"; 
        ?> 


        <form>
            <label>Search</label>
            <input type = "text" name = "search" />
            <input type = "submit" value = "Submit" />
        </form>

        <?php
        if ($_GET["search"]){
            echo "results of ". $_GET["search"];
        }
        ?>       

    </body> 
</html>