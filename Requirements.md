Requirements
============


Vehicles
--------
name
make
model
year


Locations
---------
name
lat
long
is_favorite


MaintenanceRecord
-----------------
name
vehicle
mileage
location
date
comment


Fillups [extends MaintenanceRecord]
-----------------------------
gallons
price_per_gallon
grade


Trips
-----
name


Alerts
------
maintenance_record_type
mileage
date
reminder_type