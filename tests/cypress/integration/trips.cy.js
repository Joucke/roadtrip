describe('trips - a logged in user', () => {
  beforeEach(() => {
    cy.refreshDatabase();
    cy.login()
  });

  it('can create a new trip', () => {
    cy.visit('/dashboard')

      .get('a#create-trip')
      .click()

      .get('input#title')
      .type('My First Roadtrip')

      .get('button#create')
      .click()

      .assertRedirect(Cypress.Laravel.route('trips.show', { trip: 1 }))

      .get('body')
      .contains(/my first roadtrip/i)
  })

  it('can invite users to a trip', () => {
    cy.seed('TripUserSeeder')
      .php(`
        App\\Models\\User::all();
      `).then(users => {
      cy.visit('/trips/1')

        .get('select#user')
        .select('2')

        .get('button#add-user')
        .click()

      users.forEach(u => {
        cy.get('body')
          .contains(u.name)
      })
    })
  })

  it.only('can remove users from a trip', () => {
    cy.seed('TripUserSeeder')
      .php(`
        tap(App\\Models\\Trip::first(), fn($t) => $t->users()->attach(2));
      `).php(`
        App\\Models\\User::all();
      `).then(users => {
        cy.visit('/trips/1')

          .get('button#remove-user-2')
          .click()

          .assertRedirect('/trips/1')

        cy.contains('div#user-list', users.find(u => u.id == 2).name)
          .should('not.exist')
      })
  })

  it('can see their last updated trip on their dashboard', () => {
    cy.seed('TripUserSeeder')
      .php(`
        App\\Models\\Trip::latest('updated_at')->first();
      `).then(trip => {
        cy.visit('/dashboard')
          .get(`a#continue-${trip.id}`)
          .click()

          .get('body')
          .contains(trip.title)
      })
  })
})
