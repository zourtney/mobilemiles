class AddPriceToMaintenanceRecord < ActiveRecord::Migration
  def change
    add_column :maintenance_records, :price, :float
  end
end
