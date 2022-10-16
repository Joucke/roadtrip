describe('authentication', () => {
  beforeEach(() => {
    cy.refreshDatabase();
  });

  it('can register a new user')

  it('can login', () => {
    cy.create('App\\Models\\User', {name: 'JohnDoe'})
      .then(user => {
        cy.visit('/')
          .get('a#login')
          .click()

          .get('input#email')
          .type(user.email)
          .click()

          .get('input#password')
          .type('password')
          .click()

          .get('button#login')
          .click()

          .get('div#logged-in-user')
          .contains(user.name)
      })
  })

  it('can logout')
})
