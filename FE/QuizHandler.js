const quizData = [
    {
        question: "What is the capital of France?",
        options: ["London", "Berlin", "Paris", "Madrid"],
        correct: 2
    },
    {
        question: "Which planet is known as the Red Planet?",
        options: ["Venus", "Mars", "Jupiter", "Saturn"],
        correct: 1
    },
    {
        question: "Who painted the Mona Lisa?",
        options: ["Vincent van Gogh", "Pablo Picasso", "Leonardo da Vinci", "Michelangelo"],
        correct: 2
    },
    {
        question: "What is the largest ocean on Earth?",
        options: ["Atlantic Ocean", "Indian Ocean", "Arctic Ocean", "Pacific Ocean"],
        correct: 3
    },
    {
        question: "Which element has the chemical symbol 'O'?",
        options: ["Gold", "Silver", "Oxygen", "Iron"],
        correct: 2
    },
    
];

let currentQuestion = 0;
let score = 0;
let timer;
let timeLeft = 30;

const questionEl = document.getElementById('question');
const optionsEl = document.getElementById('options');
const nextBtn = document.getElementById('next-btn');
const checkBtn = document.getElementById('check-btn');
const timerEl = document.getElementById('timer');
const progressBar = document.querySelector('.progress-bar');
const quizContainer = document.getElementById('quiz');
const questionNumberEl = document.getElementById('question-number');
const scoreContainer = document.getElementById('score-container');

function isSessionActive(){
    const username = localStorage.getItem('username');
    if(username){
        const params = new URLSearchParams();
        params.append('username', localStorage.getItem('username'));
        const queryString = params.toString();
        const url = `http://localhost:80/login.php?${queryString}`;
        return fetch(url, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data =>{ 
            console.log(data.status);
            return data.status;
        })
    }else{
        return false;
    }
}

function loadQuestion() {
    if (!isSessionActive()) {
        console.log("Redirect");
        window.location.href = 'http://localhost:5500/login.html';
    } else {
       
        const params = new URLSearchParams();
        params.append('username', localStorage.getItem('username'));
        const queryString = params.toString();
        const url = `http://localhost:80/login.php?${queryString}`;
        
        
        fetch(url, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            
            if (data.has_taken_quiz) {
                quizContainer.innerHTML = `
                    <div class="results">
                        <h2>You have already taken this quiz!</h2>
                        <p>Each user can only take the quiz once.</p>
                        <button class="btn btn-primary btn-custom" onclick="window.location.href='login.html'">Back to Login</button>
                    </div>
                `;
                return;
            }
            
            
            const question = quizData[currentQuestion];
            questionEl.textContent = question.question;
            optionsEl.innerHTML = '';

            question.options.forEach((option, index) => {
                const button = document.createElement('button');
                button.textContent = option;
                button.classList.add('option');
                button.addEventListener('click', () => selectOption(button, index));
                optionsEl.appendChild(button);
            });
            nextBtn.style.display = 'none';
            checkBtn.style.display = 'block';
            timeLeft = 30;
            if (timer) clearInterval(timer);
            startTimer();
            updateProgress();
            updateQuestionNumber();
        });
    }
}
let selectedOptionIndex = null;
function selectOption(selectedButton, optionIndex) {
    const buttons = optionsEl.getElementsByClassName('option');
    Array.from(buttons).forEach(button => button.classList.remove('selected'));
    selectedButton.classList.add('selected');

    // Store the selected option index
    selectedOptionIndex = optionIndex;

    //Show next Button
    nextBtn.style.display = 'block';
}

function startTimer() {
    timer = setInterval(() => {
        timeLeft--;
        timerEl.textContent = `Time: ${timeLeft}s`;
        if (timeLeft === 0) {
            clearInterval(timer);
            checkAnswer();
        }
    }, 1000);
}

function checkAnswer() {
    const question = quizData[currentQuestion];

    if (selectedOptionIndex !== null) {
        const selectedButton = optionsEl.children[selectedOptionIndex];
        if (selectedOptionIndex === question.correct) {
            score++;
            selectedButton.classList.add('correct');
        } else {
            selectedButton.classList.add('incorrect');
            optionsEl.children[question.correct].classList.add('correct');
        }
    } else {
        optionsEl.children[question.correct].classList.add('correct');
    }

    // Disable all options after checking the answer
    Array.from(optionsEl.children).forEach(button => button.disabled = true);

    // Show the Next button and hide the Check Answer button
    nextBtn.style.display = 'block';
    checkBtn.style.display = 'none';
    clearInterval(timer);
}

function updateProgress() {
    const progress = ((currentQuestion + 1) / quizData.length) * 100;
    progressBar.style.width = `${progress}%`;
    progressBar.setAttribute('aria-valuenow', progress);
}

function showResults() {

    const username = localStorage.getItem('username');
    console.log("Fetched username from localStorage:", username);
    if(username){
        const formData = new FormData();
        formData.append('username', username);
        formData.append('score', score);

        fetch("http://localhost:80/SendScore.php",{
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data =>{
            if(data.success){
                quizContainer.innerHTML = `
                <div class="results">
                    <div class="result-icon">
                        <i class="fas ${score > quizData.length / 2 ? 'fa-trophy text-success' : 'fa-times-circle text-danger'}"></i>
                    </div>
                    <div class="score">Your score: ${score}/${quizData.length}</div>
                    <p>${score > quizData.length / 2 ? 'Great job!' : 'Better luck next time!'}</p>
                    
                </div>
            `;
            }else{
                alert(data.message || 'Cannot save data');
            }
        })
        .catch(error => console.error('Error:', error));

    }else{
        alert('Username not found, Please log in again');
        window.location.href = 'http://localhost:5500/login.html';
    }
}



nextBtn.addEventListener('click', () => {
    if (checkBtn.style.display !== 'none') {
        checkAnswer();
    }
    currentQuestion++;
    if (currentQuestion < quizData.length) {
        loadQuestion();
    } else {
        showResults();
    }
});

function updateQuestionNumber(){
    if (currentQuestion === quizData.length - 1) {
        questionNumberEl.textContent = "Final Question";
    } else {
        questionNumberEl.textContent = `Question No: ${currentQuestion + 1}`;
    }
}

checkBtn.addEventListener('click', checkAnswer);
    

loadQuestion();






