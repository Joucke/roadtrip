describe('regions - a logged in user', () => {
  beforeEach(() => {
    cy.refreshDatabase()
    cy.login()
  })

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

  it('can switch from overview to region view', () => {
    cy.seed('TripSeeder')
    cy.php(`
      App\\Models\\Region::factory()->create([
        'title' => 'Region',
        'trip_id' => 1,
      ]);
    `)
    cy.visit(Cypress.Laravel.route('trips.show', {trip: 1}))
      .get('a#link-region-1')
      .click()

    cy.get('h1#header-region-1')
      .contains('Region')
  })

  it('can switch from region view to overview', () => {
    cy.seed('TripSeeder')
    cy.php(`
      App\\Models\\Region::factory()->create([
        'title' => 'Region',
        'trip_id' => 1,
      ]);
    `)
    cy.visit(Cypress.Laravel.route('trips.show', {trip: 1}) + '#region,1')

    cy.get('h1#header-region-1')
      .contains('Region')

    cy.get('a#back-to-trip-overview')
      .click()

    cy.get('#regions [cy-id="region-row"]')
      .contains('Region')
  })

  it('can open the edit form for fields of a region', () => {
    cy.seed('TripSeeder')
    cy.php(`
      App\\Models\\Region::factory()->create([
        'title' => 'Original title',
        'trip_id' => 1,
      ]);
    `)

    cy.visit(Cypress.Laravel.route('trips.show', { trip: 1 }) + '#region,1')

    cy.get('a#edit-region-1')
      .click()

    cy.location()
      .should(loc => {
        expect(loc.hash).to.eq('#region,1,edit')
      })

    cy.get('#edit-region-1 input:visible')
      .its('length')
      .should('eq', 2)
  })

  it('can close the edit form for fields of a region', () => {
    cy.seed('TripSeeder')
    cy.php(`
      App\\Models\\Region::factory()->create([
        'title' => 'Original title',
        'trip_id' => 1,
      ]);
    `)

    cy.visit(Cypress.Laravel.route('trips.show', { trip: 1 }) + '#region,1,edit')

    cy.get('h1#header-region-1>a')
      .contains('×')
      .click()

    cy.location()
      .should(loc => {
        expect(loc.hash).to.eq('#region,1')
      })

    cy.get('input#region-title-1')
      .should('not.exist')

    cy.get('input#region-arrival_at-1')
      .should('not.exist')
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

    cy.visit(Cypress.Laravel.route('trips.show', {trip: 1}) + '#region,1')

    cy.get('a#edit-region-1')
      .click()

    cy.get('input#region-title-1')
      .clear()
      .type('A new title')
      .blur()

    cy.wait('@region-patch')

    cy.get('h1#header-region-1')
      .contains('A new title')

    // TODO: confirm the new title still exists after a reload
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

    cy.get('[cy-id="region-row"]')
      .first()
      .contains('First region')

    cy.get('[cy-id="region-row"]')
      .eq(1)
      .contains('Second region')
      .click()

    cy.get('a#edit-region-2')
      .click()

    cy.get('input#region-arrival_at-2')
      .clear()
      .type('2022-07-05T10:00:00')
      .blur()

    cy.wait('@region-patch')

    cy.get('a#back-to-trip-overview')
      .click()

    cy.get('[cy-id="region-row"]')
      .first()
      .contains('Second region')

    cy.get('[cy-id="region-row"]')
      .eq(1)
      .contains('First region')
  })
})
