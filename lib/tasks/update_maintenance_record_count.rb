# Reset maintenance record count.
#
# Run with:
#   rails runner lib/tasks/update_maintenance_record_count.rb
#
# See:
#   http://stackoverflow.com/a/9400828/311207
#   http://railscasts.com/episodes/23-counter-cache-column?view=comments#comment_156195
Vehicle.select(:id).find_each do |v|
  Vehicle.reset_counters(v.id, :maintenance_records)
end