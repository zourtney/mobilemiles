class FillupsController < AuthorizedController
  def index
    record = Fillup.where(user_id: @user.id)
    render :json => record
  end

  def show
  end

  def create
    record = Fillup.new(fillups_params)
    record.user_id = @user.id
    record.save
    render :json => record
  end

  def update
  end

  def delete
  end

private
  def fillups_params
    params.require(:fillups).permit(:name, :vehicle, :gallons, :price_per_gallon, :grade, :mileage, :completed_at, :comment)
  end
end
