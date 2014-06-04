# This file should contain all the record creation needed to seed the database with its default values.
# The data can then be loaded with the rake db:seed (or created alongside the db with db:setup).
#
# Examples:
#
#   cities = City.create([{ name: 'Chicago' }, { name: 'Copenhagen' }])
#   Mayor.create(name: 'Emanuel', city: cities.first)

guestUser = User.create(
  email: 'guest@example.com',
  password: 'guest123'
)

guestVehicle1 = Vehicle.create(
  name: 'Family car',
  make: 'Toyota',
  model: 'Camry',
  year: 2006,
  user: guestUser
)

