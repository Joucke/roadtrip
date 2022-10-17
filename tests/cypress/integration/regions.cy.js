describe('regions - a logged in user', () => {
  beforeEach(() => {
    cy.refreshDatabase();
    cy.login()
  });

  it.only('can add a region to a trip', () => {
    cy.seed('TripUserSeeder')
      .visit(Cypress.Laravel.route('trips.show', {trip: 1}))

      .get('button#add-region')
      .click()

      .get('input#region_title')
      .type('Eifel,Germany')

      .get('#region_result')
      .contains('Eifel')

      .click()

      // .get('#regions')
      // .contains('Eifel')
  })
})
