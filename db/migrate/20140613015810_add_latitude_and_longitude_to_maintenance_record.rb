class AddLatitudeAndLongitudeToMaintenanceRecord < ActiveRecord::Migration
  def change
    add_column :maintenance_records, :latitude, :float
    add_column :maintenance_records, :longitude, :float
  end
end
