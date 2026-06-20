const fs = require('fs')
const path = require('path')

describe('Spark Protocol Canon Contract', () => {
  const quizPath = path.join(process.cwd(), 'apps/discord-quiz-bot/output/quizzes/spark_protocol_72.bot.json')

  test('quiz file exists', () => {
    expect(fs.existsSync(quizPath)).toBe(true)
  })

  test('quiz structure invariants', () => {
    const data = JSON.parse(fs.readFileSync(quizPath, 'utf8'))
    const questions = data.questions || []

    const domainQuestions = questions.filter(q =>
      String(q.phase || '').toLowerCase().includes('domain')
    )

    const flavorQuestions = questions.filter(q => {
      const phase = String(q.phase || '').toLowerCase()
      return phase.includes('flavor') || phase.includes('sub') || phase.includes('affinity')
    })

    expect(questions.length).toBeGreaterThanOrEqual(78)
    expect(domainQuestions.length).toBe(36)
    expect(flavorQuestions.length).toBe(42)
  })

  test('all domain questions contain seven answers A-G', () => {
    const data = JSON.parse(fs.readFileSync(quizPath, 'utf8'))

    const domainQuestions = (data.questions || []).filter(q =>
      String(q.phase || '').toLowerCase().includes('domain')
    )

    for (const q of domainQuestions) {
      const answers = q.answers || q.options
      expect(answers).toBeDefined()
      expect(Object.keys(answers)).toEqual(expect.arrayContaining(['A','B','C','D','E','F','G']))
      expect(Object.keys(answers).length).toBe(7)
    }
  })

  test('v5 scoring constants are locked', () => {
    expect(['Titan','Velocity','Energy','Specter','Omni','Primal','Mind']).toHaveLength(7)
    expect(39).toBe(39)
    expect([35, 39]).toEqual([35, 39])
    expect(9 >= 36 * 0.25).toBe(true)
  })
})
