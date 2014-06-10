class AddMaintenanceRecordsCountToVehicle < ActiveRecord::Migration
  def change
    add_column :vehicles, :maintenance_records_count, :integer, :default => 0
  end
end
