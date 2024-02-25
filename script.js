// Selecting DOM elements
const form = document.querySelector('form');
const weekContainer = document.querySelector('.weekContainer');
const weatherInfoDiv = document.getElementById('weatherInfo');

// Function to display weather information
const displayWeather = (data) => {

    // HTML contents to display weather information
    const htmlContent = `
        <div class="dateTime">
            <p class="time">${data[0].locTime}</p>
            <p class="time">${data[0].dayOfWeek.substring(0, 3)} ${data[0].monthDate}</p>
        </div>

        <div class="city">
            <h2 class="fa-solid fa-location-dot"></h2>
            <h2>${data[0].cityName}, ${data[0].country}</h2>
        </div>
        <div class="weatherImg">
            <img src="img/${data[0].weatherIcon}.svg" alt="weather-icon" />
        </div>
        <h4>${data[0].weatherDescription}</h4>

        
        <h1>${Math.round(data[0].temperature)} °C</h1>
        <p class="feelsLike">Feels like: ${Math.round(data[0].feelsLikeTemp)} °C</p>
        
        <hr>

        <section class="weatherInfo2">

            <div class="pressure">
                <figure><img src="img/pressure.png" alt="Pressure Icon"></figure>
                <h3>${data[0].pressure.slice(0, 4)} hPa</h3>
                <p>Pressure</p>
            </div>

            <div class="windSpeed">
                <figure><img src="img/windSpeed.png" alt="Wind Speed Icon"></figure>
                <h3>${data[0].windSpeed} m/s</h3>
                <p>Wind Speed</p>
            </div>

            <div class="humidity">
                <figure><img src="img/humidity.png" alt="Humidity Icon"></figure>
                <h3>${data[0].humidity}%</h3>
                <p>Humidity</p>
            </div>

        </section>
    `;
    weatherInfoDiv.innerHTML = htmlContent;
};

// Function to fetch weather data from the OpenWeatherMap API
const getWeatherData = async () => {
    const cityInput = document.getElementById('cityInput');
    const cityName = cityInput.value;

    try {

        const isOnline = navigator.onLine;
        let url;

        if (isOnline) {

            // Constructing the API URL
            if (cityName === "") {
                url = `http://localhost/php/Prototype3/data.php?q=Cebu%20City`;
            } else {
                url = `http://localhost/php/Prototype3/data.php?q=${cityName}`;
            }

            // Fetching weather data from the local server
            const response = await fetch(url);
            const data = await response.json();

            // Storing the weather data in local storage
            storeInLocalStorage(cityName, data);
        }

        // Displaying the weather data from local storage
        const weatherData = getFromlocalStorage(cityName);

        if (weatherData) {
            displayWeather(weatherData);
            PastWeatherData(cityName);
        } else {
            // Displaying an error message if city not found
            weatherInfoDiv.innerHTML = `
                <div class="error">
                    <figure><img src="img/error.png" alt="Error Image"></figure>
                    <h3>${cityName} is not stored in the localStorage yet!</h3>
                </div>
            `;
        }
    } catch (error) {
        console.log(error);
    }
};

// Event listener for form submission
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    await getWeatherData();
    update();
    setTimeout(() => {
        getWeatherData();
    }, 3000); // Refresh weather after 3 seconds
});

// Fetching default city weather data on page load
getWeatherData();

// Function to fetch and display past weather data
async function PastWeatherData(cityName) {
    try {
        if (cityName === "") {
            cityName = "Cebu City";
        }
        document.querySelector(".right h1").innerText = `${cityName} Past Weather`;
        // Fetching past weather data
        const response = await fetch(`http://localhost/php/Prototype3/data.php?q=${cityName}`);
        if (!response.ok) {
            throw new Error(response.status);
        } else {
            const data = await response.json();
            let weekBox = "";

            // Generating HTML for past weather data
            data.forEach(dayData => {
                weekBox += `
                <div class="week-box">
                    <div class="date"> ${dayData.monthDate}</div>
                    <div class="db-info">
                        <p>${dayData.dayOfWeek.substring(0, 3)}</p>
                        <img src="img/${dayData.weatherIcon}.svg" alt="weather-icon" />
                        <p>${Math.round(dayData.temperature)}°C</p>
                        <p>${dayData.pressure.slice(0, 4)} hPa</p>
                        <p>${dayData.windSpeed} m/s</p>
                        <p>${dayData.humidity}%</p>
                    </div>
                </div>
                <hr>
                `;
            });

            // Updating the weekContainer with the past weather data
            weekContainer.innerHTML = weekBox;

        }
    } catch (error) {
        console.log(error);
    }
}

async function update() {
    const cityInput = document.getElementById('cityInput');
    const cityName = cityInput.value;

    try {
        const url = `index.php?q=${cityName}`;
        response = await fetch(url);
        data = await response.json();
    } catch (error) {
        console.log(error);
    }
}


function storeInLocalStorage(cityName, data) {
    if (cityName === "") {
        cityName = "Cebu City";
    }
    localStorage.setItem(cityName, JSON.stringify(data)); // converts JS object to JSON string
}

function getFromlocalStorage(cityName) {
    if (cityName === "") {
        cityName = "Cebu City";
    }
    console.log(cityName);
    const data = localStorage.getItem(cityName); // returns null if not found
    if (data) {
        return JSON.parse(data); // converts JSON string to JS object
    } else {
        console.log("Weather Data not found in Local storage");
        return null;
    }
}
