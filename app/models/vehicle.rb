class Vehicle < ActiveRecord::Base
  belongs_to :user
  has_many :maintenance_records, :dependent => :destroy
end
