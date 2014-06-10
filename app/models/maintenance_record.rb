class MaintenanceRecord < ActiveRecord::Base
  belongs_to :vehicle, :counter_cache => true
  belongs_to :user
end
