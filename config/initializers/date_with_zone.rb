class ActiveSupport::TimeWithZone
  # Return all date / times as Unix epoch timestamps
  def as_json(options = {})
    to_i * 1000
  end
end