const questions = [
  {
    question: "What is the capital of France?",
    choices: ["London", "Paris", "Berlin", "Madrid"],
    answer: 1
  },
  {
    question: "2 + 2 = ?",
    choices: ["3", "4", "5", "2"],
    answer: 1
  }
];

let current = 0;

function renderQuestion() {
  const q = questions[current];
  document.getElementById("question").textContent = q.question;
  const choicesBox = document.getElementById("choices");
  choicesBox.innerHTML = "";
  q.choices.forEach((c, i) => {
    const btn = document.createElement("button");
    btn.textContent = c;
    btn.onclick = () => {
      if (i === q.answer) alert("Correct!");
      else alert("Wrong!");
    };
    choicesBox.appendChild(btn);
  });
}

function nextQuestion() {
  current++;
  if (current < questions.length) renderQuestion();
  else alert("Quiz finished!");
}

window.onload = renderQuestion;
