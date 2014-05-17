class VehiclesController < AuthorizedController
  
  def index
    vehicles = Vehicle.where(user_id: @user.id)
    render :json => vehicles
  end
  
  def show
    vehicle = Vehicle.find_by(user_id: @user.id, id: params[:id])
    if vehicle
      render :json => vehicle
    else
      render :json => { error: 'Vehicle not found' }, :status => :not_found
    end
  end
  
  def create
    vehicle = Vehicle.new(vehicle_params)
    vehicle.user_id = @user.id
    vehicle.save
    render :json => vehicle
  end
  
  def update
    vehicle = Vehicle.find_by(user_id: @user.id, id: params[:id])
    if vehicle.update(vehicle_params)
      render :json => vehicle
    #TODO: handle else -- attempts to modify an unauthorized resource
    end
  end
  
  def destroy
    vehicle = Vehicle.find_by(user_id: @user.id, id: params[:id])
    if vehicle.destroy
      render :json => {}, :status => 200
    else
      render :json => { error: 'Failed to destroy' }, :status => 500
    end
  end

private
  def vehicle_params
    params.require(:vehicle).permit(:name, :make, :model, :year)
  end

end