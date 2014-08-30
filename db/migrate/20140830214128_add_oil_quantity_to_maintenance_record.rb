class AddOilQuantityToMaintenanceRecord < ActiveRecord::Migration
  def change
    add_column :maintenance_records, :oil_quantity, :float
  end
end
