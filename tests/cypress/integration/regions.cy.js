describe.only('regions - a logged in user', () => {
  beforeEach(() => {
    cy.refreshDatabase();
    cy.login()
  });

  it('can add a region to a trip', () => {
    cy.seed('TripSeeder')

    cy.visit(Cypress.Laravel.route('trips.show', {trip: 1}))

    cy.get('button#add-region')
      .click()

    cy.get('input#region-title')
      .type('Eifel,Germany')

    cy.get('#region-result')
      .contains('Eifel')
      .click()

    cy.get('#regions')
      .contains('Eifel')

    cy.get('img.leaflet-marker-icon')
      .should('have.length', 1)
  })

  it.only('can update the title of a region', () => {
    cy.seed('TripSeeder')
    cy.php(`
      App\\Models\\Region::factory()->create([
        'title' => 'Original title',
        'trip_id' => 1,
      ]);
    `)

    cy.intercept('patch', Cypress.Laravel.route('trips.regions.update', {trip: 1, region: 1}))
      .as('region-patch')

    cy.visit(Cypress.Laravel.route('trips.show', {trip: 1}))

    cy.get('p#region-title-1')
      .contains(/original title/i)
      .click()
      .clear()
      .type('A new title')

      .blur()

    cy.wait('@region-patch')

    cy.reload()
    cy.get('p#region-title-1')
      .contains('A new title')
  })
})
