class AddGooglePlaceToMaintenanceRecord < ActiveRecord::Migration
  def change
    add_column :maintenance_records, :google_place, :string
  end
end
