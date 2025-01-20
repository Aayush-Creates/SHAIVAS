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
