class MaintenanceRecordsController < AuthorizedController
  def index
    records = type_class.where(user_id: @user.id)
    render :json => records
  end

  def show
    record = find_record
    if record
      render :json => record
    else
      render :json => { error: 'Record not found' }, :status => :not_found
    end
  end

  def create
    if not find_vehicle
      render :json => { error: 'Invalid vehicle' }, :status => :bad_request
    else
      record = type_class.new(maintenance_record_params)
      record.user_id = @user.id

      if not params[:completed_at]
        record.completed_at = Time.now
      end

      record.save
      render :json => record
    end
  end

  def update
    record = find_record
    if not record
      render :json => { error: 'Record not found' }, :status => :not_found
    else
      if not find_vehicle
        render :json => { error: 'Invalid vehicle' }, :status => :bad_request
      elsif not record.update(maintenance_record_params)
        render :json => { error: 'Failed to update record' }, :status => 500
      else
        render :json => record
      end
    end
  end

  def delete
  end

private
  def type_class
    params[:type].constantize    # http://thibaultdenizet.com/tutorial/single-table-inheritance-with-rails-4-part-3/
  end

  def find_record
    type_class.find_by(user_id: @user.id, id: params[:id])
  end

  def find_vehicle
    Vehicle.find_by(user_id: @user.id, id: params[:vehicle_id])
  end

  def maintenance_record_params
    params.require(:maintenance_record).permit(:name, :vehicle_id, :gallons, :price_per_gallon, :grade, :mileage, :price, :completed_at, :comment)
  end,
end
