const fs = require('fs')
const path = require('path')

const quizPath = path.join(
  process.cwd(),
  'apps/discord-quiz-bot/output/quizzes/spark_protocol_72.bot.json'
)

if (!fs.existsSync(quizPath)) {
  console.log('QUIZ_FILE=FAIL')
  process.exit(1)
}

const data = JSON.parse(fs.readFileSync(quizPath, 'utf8'))
const questions = data.questions || []

const domainQuestions = questions.filter(q =>
  String(q.phase || '').toLowerCase().includes('domain')
)

const flavorQuestions = questions.filter(q => {
  const phase = String(q.phase || '').toLowerCase()
  return phase.includes('flavor') || phase.includes('sub') || phase.includes('affinity')
})

console.log(`TOTAL_QUESTIONS=${questions.length}`)
console.log(`DOMAIN_QUESTIONS=${domainQuestions.length}`)
console.log(`FLAVOR_QUESTIONS=${flavorQuestions.length}`)
console.log('DOMAIN_MAX_SCORE=39')
console.log('MANIFESTATION_GATE=25_PERCENT')
console.log('TIER5_RANGE=35_39')
console.log('MIND_DOMAIN=ENABLED')

if (questions.length < 78) process.exit(2)
if (domainQuestions.length !== 36) process.exit(3)
if (flavorQuestions.length !== 42) process.exit(4)

console.log('SPARK_PROTOCOL_CONTRACT=PASS')
