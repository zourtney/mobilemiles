class AddOilChangeToMaintenanceRecord < ActiveRecord::Migration
  def change
    add_column :maintenance_records, :oil_weight, :string
    add_column :maintenance_records, :oil_type, :string
    add_column :maintenance_records, :oil_brand, :string
    add_column :maintenance_records, :oil_filter_part_number, :string
    add_column :maintenance_records, :oil_filter_brand, :string
  end
end
