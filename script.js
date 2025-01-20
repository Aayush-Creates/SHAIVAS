// Disable ETo form initially
document.getElementById("eto-form").querySelectorAll("input, button").forEach((element) => {
    element.disabled = true;
});

// Event listener for weather form submission
document.getElementById("weather-form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const location = document.getElementById("city").value;

    if (!location) {
        alert("Please enter a city name.");
        return;
    }

    try {
        // Fetch geocoding data
        const apiUrl = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(location)}&count=1&format=json`;
        const geoResponse = await fetch(apiUrl);
        const geoData = await geoResponse.json();

        if (!geoData.results || geoData.results.length === 0) {
            alert("Location not found. Please try another city.");
            return;
        }

        const { latitude, longitude } = geoData.results[0];

        // Fetch weather data
        const weatherUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current_weather=true`;
        const weatherResponse = await fetch(weatherUrl);
        const weatherData = await weatherResponse.json();

        const temperature = weatherData.current_weather.temperature;

        // Update weather data in the weather-output section
        document.getElementById("weather-output").innerHTML = `
            <h2>Weather Analysis</h2>
            <p>City: ${location}</p>
            <p>Temperature: ${temperature}°C</p>
        `;

        // Enable ETo form after successful weather data fetch
        document.getElementById("eto-form").querySelectorAll("input, button").forEach((element) => {
            element.disabled = false;
        });
    } catch (error) {
        console.error("Error fetching weather data:", error);
        alert("An error occurred while fetching weather data. Please try again.");
    }
});

// Event listener for ETo form submission
document.getElementById("eto-form").addEventListener("submit", (e) => {
    e.preventDefault();

    const cropType = document.getElementById("crop-type").value;

    if (!cropType) {
        alert("Please enter a crop type.");
        return;
    }

    // Simple ETo calculation logic based on crop type
    let etoValue;
    let waterSuggestion;
    let additionalRequirements;

    switch (cropType.toLowerCase()) {
        case "wheat":
            etoValue = 5.0;
            waterSuggestion = "Moderate irrigation required.";
            additionalRequirements = "Requires well-drained loamy soil and full sunlight.";
            break;
        case "rice":
            etoValue = 6.5;
            waterSuggestion = "High water requirement, ensure flooded fields.";
            additionalRequirements = "Prefers clayey soil and warm temperatures.";
            break;
        case "corn":
            etoValue = 4.8;
            waterSuggestion = "Requires frequent watering, especially during flowering.";
            additionalRequirements = "Needs fertile, well-drained soil and consistent sunlight.";
            break;
        case "cotton":
            etoValue = 6.0;
            waterSuggestion = "High water requirement; ensure deep watering during growth stages.";
            additionalRequirements = "Thrives in sandy-loam soil with warm, dry conditions.";
            break;
        default:
            etoValue = 4.0;
            waterSuggestion = "General irrigation needed based on local conditions.";
            additionalRequirements = "Ensure fertile soil and regular weeding.";
            break;
    }

    // Append ETo data in the eto-output section
    const etoOutput = `
        <h2>Crop ETo Calculation</h2>
        <p>Crop Type: ${cropType}</p>
        <p>Evapotranspiration (ETo): ${etoValue} mm/day</p>
        <p><strong>Water Suggestion:</strong> ${waterSuggestion}</p>
        <p><strong>Additional Requirements:</strong> ${additionalRequirements}</p>
    `;

    document.getElementById("eto-output").innerHTML = etoOutput;
});

// Event listener for market form submission
document.getElementById("market-form").addEventListener("submit", async (e) => {
    e.preventDefault(); // Prevent form submission

    const city = document.getElementById("city").value;
    const crop = document.getElementById("crop").value;

    if (!city || !crop) {
        alert("Please provide both city and crop.");
        return;
    }

    try {
        // Send AJAX request to the PHP script with city and crop data
        const response = await fetch('get_crop_price.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ city: city, crop: crop })
        });

        const data = await response.json();

        if (data.success) {
            // Display the fetched price data
            document.getElementById("crop-price-output").innerHTML = `
                <h3>Price for ${data.crop} in ${data.city}:</h3>
                <p>Price: ₹${data.price} per unit</p>
            `;
        } else {
            // Display error message if no data is found
            document.getElementById("crop-price-output").innerHTML = `
                <p>${data.message}</p>
            `;
        }
    } catch (error) {
        console.error("Error fetching market price:", error);
        alert("An error occurred while fetching market price. Please try again.");
    }
});
