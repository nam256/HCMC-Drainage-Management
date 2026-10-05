<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Cong extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     *
     * @var string
     */
    protected $table = 'cong';

    /**
     * Primary key của bảng (PostGIS thường dùng ogc_fid)
     *
     * @var string
     */
    protected $primaryKey = 'ogc_fid';

    /**
     * Kiểu của primary key
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * Tự động tăng primary key
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Không dùng timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     * Lưu ý: 'geom' không có trong fillable vì dùng DB::raw()
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stt',
        'id',
        'ten_cong_dap',
        'ly_trinh',
        'toa_do_x',
        'toa_do_y',
        'cum_cong_tri',
        'goi_thau',
        'loai_cong_tr',
        'hinh_thuc',
        'chieu_dai',
        'duong_kinh',
        'be_rong',
        'chieu_cao',
        'so_cua',
        'cao_trinh_da',
        'cao_trinh_di',
        'hinh_thuc_va',
        'muc_tieu_nhi',
        'dien_tich_ph',
        'dia_diem',
        'nam_su_dung',
        'cap_cong_tri',
        'he_thong_con',
        'don_vi_quan_l',
        'ghi_chu',
        'nam_cap_nhat',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
    ];

    /**
     * Các trường nên được cast
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stt' => 'integer',
        'id' => 'string',
        'toa_do_x' => 'decimal:15',
        'toa_do_y' => 'decimal:15',
        'chieu_dai' => 'integer',
        'duong_kinh' => 'integer',
        'be_rong' => 'decimal:15',
        'chieu_cao' => 'decimal:15',
        'so_cua' => 'integer',
        'dien_tich_ph' => 'integer',
        'nam_su_dung' => 'integer',
        'nam_cap_nhat' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Lấy URL của file đính kèm
     *
     * @return string|null
     */
    public function getFileUrlAttribute(): ?string
    {
        if (empty($this->file_path)) {
            return null;
        }

        return asset('storage/' . $this->file_path);
    }

    /**
     * Kiểm tra xem có file đính kèm không
     *
     * @return bool
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path) && Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Xóa file đính kèm
     *
     * @return bool
     */
    public function deleteFile(): bool
    {
        if ($this->hasFile()) {
            Storage::disk('public')->delete($this->file_path);
            $this->file_path = null;
            $this->file_name = null;
            $this->file_mime = null;
            $this->file_size = null;
            return $this->save();
        }

        return false;
    }

    /**
     * Scope: Lọc theo tên công đập
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tenCongDap
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTenCongDap($query, string $tenCongDap)
    {
        return $query->where('ten_cong_dap', 'like', "%{$tenCongDap}%");
    }

    /**
     * Scope: Lọc theo loại công trình
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $loaiCongTr
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLoaiCongTr($query, string $loaiCongTr)
    {
        return $query->where('loai_cong_tr', $loaiCongTr);
    }

    /**
     * Scope: Lọc theo địa điểm
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $diaDiem
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDiaDiem($query, string $diaDiem)
    {
        return $query->where('dia_diem', 'like', "%{$diaDiem}%");
    }

    /**
     * Scope: Lọc theo cấp công trình
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $capCongTri
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCapCongTri($query, string $capCongTri)
    {
        return $query->where('cap_cong_tri', $capCongTri);
    }
}

