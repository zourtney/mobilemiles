class CreateMaintenanceRecords < ActiveRecord::Migration
  def change
    create_table :maintenance_records do |t|
      t.string :type
      t.string :name
      t.references :vehicle, index: true
      t.references :user, index: true
      t.integer :mileage
      t.datetime :completed_at
      t.text :comment
      t.float :gallons
      t.float :price_per_gallon
      t.string :grade

      t.timestamps
    end
  end
end
