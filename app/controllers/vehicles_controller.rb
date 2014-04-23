class VehiclesController < ApplicationController
  def index
    @vehicles = Vehicle.all()
    render :json => @vehicles
  end
  def show
    @vehicle = Vehicle.find(params[:id])
    render :json => @vehicle
  end
  def create
    @vehicle = Vehicle.new(vehicle_params)
    @vehicle.save
    render :json => @vehicle
  end
  def update
    @vehicle = Vehicle.find(params[:id])
    if @vehicle.update(vehicle_params)
      render :json => @vehicle
    #TODO: handle else...if it could ever happen
    end
  end
  def destroy
    @vehicle = Vehicle.find(params[:id])
    if @vehicle.destroy
      render :json => {}, :status => 200
    else
      render :json => { error: 'Bad stuff' }, :status => 500
    end
  end

  private
    def vehicle_params
      params.require(:vehicle).permit(:name, :make, :model, :year)
    end
end
