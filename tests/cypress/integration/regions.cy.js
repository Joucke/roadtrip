describe('regions - a logged in user', () => {
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

  it('can update the title of a region', () => {
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

  it('can updated the arrival datetime of a region', () => {
    cy.seed('TripSeeder')
    cy.php(`
      App\\Models\\Region::factory()->create([
        'title' => 'First region',
        'trip_id' => 1,
        'arrival_at' => '2022-07-06 10:00:00',
      ]);
    `).php(`
      App\\Models\\Region::factory()->create([
        'title' => 'Second region',
        'trip_id' => 1,
        'arrival_at' => '2022-07-07 10:00:00',
      ]);
    `)

    cy.intercept('patch', Cypress.Laravel.route('trips.regions.update', { trip: 1, region: 2 }))
      .as('region-patch')

    cy.visit(Cypress.Laravel.route('trips.show', { trip: 1 }))

    cy.get('input#arrival-at-2')
      .clear()
      .type('2022-07-05T10:00:00')
      .blur()

    cy.wait('@region-patch')

    cy.get('[cy-id="region-row"]')
      .first()
      .contains('Second region')

    cy.get('[cy-id="region-row"]')
      .eq(1)
      .contains('First region')
  })
})
