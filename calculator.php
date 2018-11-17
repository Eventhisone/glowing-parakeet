<?php # Script 3.10 - calculator.php #5

// This function creates a radio button.
// The function takes one argument: the value.
// The function also makes the button "sticky."
function create_gallon_radio(string $value, string $name = 'gallon_price'): void {

    // Start the element
    echo '<input type="radio" name="' . $name . '" value="' . $value . '"';

    // Check for stickiness
    if( isset($_POST[$name]) && ($_POST[$name] == $value)) {
        echo 'checked="checked"';
    }

    echo "> $value ";
}

// This function calculates the cost of the trip.
// The function takes three arguments: the distance, the fuel efficiency, and the price per gallon
// The function returns the total cost.
function calculate_trip_cost(int $miles, int $mpg, int $ppg): string {

    // Get the number of gallons
    $gallons = $miles / $mpg;

    // Get the cost of those gallons:
    $dollars = $gallons * $ppg;

    // Return the formatted cost:
    return number_format($dollars, 2);
}

$page_title = 'Trip Cost Calculator';
include('includes/header.html');

// Check for form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Minimal form validation
    if (isset($_POST['distance'], $_POST['gallon_price'], $_POST['efficiency']) &&
     is_numeric($_POST['distance']) && is_numeric($_POST['gallon_price']) &&
     is_numeric($_POST['efficiency'])) {
        
        // Calculate the results
        $cost = calculate_trip_cost($_POST['distance'], $_POST['efficiency'], $_POST['gallon_price']);
        $hours = $_POST['distance'] / 65;

        // Print the results
        echo '<div class="page-header"><h1>Estimated Cost</h1></div>
        <p>The total cost of driving ' . $_POST['distance'] . ' miles, averaging ' . 
        $_POST['efficiency'] . ' miles per gallon, and paying an average of $' .
        $_POST['gallon_price'] . ' per gallon, is $' . $cost . '. 
        If you drive at an average of 65 miles per hour, The trip will take approximately ' . 
        number_format($hours, 2) . ' hours.</p>';

     } else { // Invalid submitted values
        echo '<div class="page-header><h1>Error!</h1></div>
        <p class="text-danger"> Please enter a valid distance, price per gallon, and fuel 
        efficiency.</p>'; 

     }
} // End of main submission if
?>

<div class="page-header"><h1>Trip Cost Calculator</h1></div>
<form action="calculator.php" method="post">
    <p>Distance (in miles): <input type="number" name="distance" value="<?php if 
    (isset($_POST['distance'])) echo $_POST['distance']; ?>" ></p>
        <p>Ave. Price Per Gallon: 
        <?php
        create_gallon_radio("3.00");
        create_gallon_radio("3.50");
        create_gallon_radio("4.00");
        ?>
    </p>
    <p>Fuel Efficiency: <select name="efficiency">
        <option value="10" <?php if (isset($_POST['efficiency']) && ($_POST['efficiency'] 
        == '10')) echo ' selected="selected"'; ?>>Terrible</option>
        <option value="20" <?php if(isset($_POST['efficiency']) && ($_POST['efficiency']
        == '20')) echo ' selected="selected"'; ?>>Decent</option>
        <option value="30" <?php if(isset($_POST['efficiency']) && ($_POST['efficiency']
        == '30')) echo ' selected="selected"'; ?>>Very Good</option>
        <option value="50" <?php if(isset($_POST['efficiency']) && ($_POST['efficiency']
        == '50')) echo ' selected="selected"'; ?>>Outstanding</option>
    </select>
    </p>
    <p><input type="submit" name="submit" value="Calculate!"></p>
</form>

<?php include('includes/footer.html'); ?>
