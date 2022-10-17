describe('authentication', () => {
  beforeEach(() => {
    cy.refreshDatabase();
  });

  it('can register a new user', () => {
    cy.visit(Cypress.Laravel.route('home'))

      .get('a#register')
      .click()

      .get('input#name')
      .type('John Doe')

      .get('input#email')
      .type('john@example.com')

      .get('input#password')
      .type('password')

      .get('input#password_confirmation')
      .type('password')

      .get('button#register')
      .click()

      .assertRedirect(Cypress.Laravel.route('dashboard'))

      .get('#logged-in-user')
      .contains('John Doe')

      .get('main')
      .contains(/you're logged in/i)
  })

  it('can login', () => {
    cy.create('App\\Models\\User', {name: 'JohnDoe'})
      .then(user => {
        cy.visit(Cypress.Laravel.route('home'))
          .get('a#login')
          .click()

          .get('input#email')
          .type(user.email)

          .get('input#password')
          .type('password')

          .get('button#login')
          .click()

          .get('div#logged-in-user')
          .contains(user.name)
      })
  })

  it('can logout', () => {
    cy.login()
      .visit(Cypress.Laravel.route('dashboard'))
      .get('#logged-in-user')
      .click()

      .get('a#logout:visible')
      .click()

      .assertRedirect(Cypress.Laravel.route('home'))
  })
})
