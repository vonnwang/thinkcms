<?php
namespace socket;
/* +----------------------------------------------------------------------
 | 框架扩展 php 收发socket包基本操作
 +----------------------------------------------------------------------
 | Author: Dayongwang <xinglinxueba@163.com>
 +----------------------------------------------------------------------
 | Date:2018/07/24
 +----------------------------------------------------------------------
*/

class Socketpacket
{
	const PACKET_BUFFER_SIZE = 8192; // 包最大长度
	const PACKET_HEADER_SIZE = 12; // 包头长度
	const GAME_ID = 1; // 暂无用到的一个ID
	
	/**
	 * 包内容
	 * 
	 * @var string
	 */
	private $m_packetBuffer;
	
	/**
	 * 包长度
	 * 
	 * @var int
	 */
	private $m_packetSize;
	
	/**
	 * 命令字
	 * 
	 * @var int
	 */
	public $m_CmdType;
	
	/**
	 * 构造函数
	 */
	public function __construct() 
	{
		$this->m_packetSize = 0;
		$this->m_packetBuffer = "";
	}
	
	/**
	 * 开始写包数据
	 * 
	 * @param int $CmdType
	 *        	命令字
	 */
	public function WriteBegin( $CmdType) 
	{
		$this->m_packetSize = 0;
		$this->m_packetBuffer = "";
		$this->m_CmdType = $CmdType;
	}
	
	/**
	 * 结束写包数据
	 */
	public function WriteEnd() 
	{
		$content = pack ( "N", $this->m_packetSize + self::PACKET_HEADER_SIZE - 4 );
		$content .= "HS";
		$content .= pack ( "N", $this->m_CmdType ); // cmd
		$content .= pack ( "n", self::GAME_ID ); // gameid
		$this->m_packetBuffer = $content . $this->m_packetBuffer;
	}
	
	/**
	 * 取包内容
	 * 
	 * @return string 包内容
	 */
	public function GetPacketBuffer() 
	{
		return $this->m_packetBuffer;
	}
	
	/**
	 * 取包长度
	 * 
	 * @return int 包长度
	 */
	public function GetPacketSize() 
	{
		return $this->m_packetSize + self::PACKET_HEADER_SIZE;
	}
	
	/**
	 * 写一个INT类型值
	 * 
	 * @param int $value
	 *        	值
	 */
	public function WriteInt($value) 
	{
		$this->m_packetBuffer .= pack ( "N", $value );
		$this->m_packetSize += 4;
	}
	/**
	 * 写一个int 64类型值
	 * 
	 * @param int $value        	
	 */
	public function WriteInt64($value) 
	{
		$str = pack ( "N", $value >> 32 );
		$str .= pack ( "N", $value & 0xffffffff );
		$this->m_packetBuffer .= $str;
		$this->m_packetSize += 8;
	}
	/**
	 * 写一个无符号INT类形值
	 * 
	 * @param int $value
	 *        	值
	 */
	public function WriteUInt($value) 
	{
		$this->m_packetBuffer .= pack ( "N", $value );
		$this->m_packetSize += 4;
	}
	
	/**
	 * 写一个Byte类型值
	 * 
	 * @param int $value
	 *        	值
	 */
	public function WriteByte($value) 
	{
		$this->m_packetBuffer .= pack ( "C", $value );
		$this->m_packetSize += 1;
	}
	
	/**
	 * 写一个Short类型值
	 * 
	 * @param int $value
	 *        	值
	 */
	public function WriteShort($value) 
	{
		$this->m_packetBuffer .= pack ( "n", $value );
		$this->m_packetSize += 2;
	}
	
	/**
	 * 写一个无符号字符串类型值
	 * 
	 * @param string $value
	 *        	值
	 */
	public function WriteString($value) 
	{
		$len = strlen ( $value ) + 1;
		$this->m_packetBuffer .= pack ( "N", $len );
		$this->m_packetBuffer .= $value;
		$this->m_packetBuffer .= pack ( "C", 0 );
		$this->m_packetSize += $len + 4;
	}
	
	/**
	 * 解析包内容
	 * 
	 * @return int 状态 0:成功 其它值:失败
	 */
	public function ParsePacket() 
	{
		if ($this->m_packetSize < self::PACKET_HEADER_SIZE) 
		{
			return 1;
		}
		
		$header = substr ( $this->m_packetBuffer, 0, self::PACKET_HEADER_SIZE );
		$arr = unpack ( "NLen/c2Iden/NCmdType/nGameId", $header );
		if ($arr ['Iden1'] != ord ( 'H' ) || $arr ['Iden2'] != ord ( 'S' )) 
		{
			return - 1;
		}
		
		if ($arr ['CmdType'] <= 0 || $arr ['CmdType'] >= 39321) 
		{
			return - 3;
		}
		
		if ($arr ['Len'] >= 0 && $arr ['Len'] > self::PACKET_BUFFER_SIZE - self::PACKET_HEADER_SIZE) 
		{
			return - 4;
		}
		
		$this->m_packetBuffer = substr ( $this->m_packetBuffer, self::PACKET_HEADER_SIZE );
				
		return 0;
	}
	/**
	 * swoole解析包内容
	 * 
	 * @param unknown $packet_buff        	
	 * @return int 状态 1:成功 其它值:失败
	 */
	public function swooleParsePacket($packet_buff) 
	{
		$this->m_packetBuffer = $packet_buff;
		
		$header = substr ( $this->m_packetBuffer, 0, self::PACKET_HEADER_SIZE );
		$arr = unpack ( "NLen/c2Iden/NCmdType/nGameId", $header );
		$this->m_packetSize = $arr ['Len'];
		if (($this->m_packetSize < self::PACKET_HEADER_SIZE) || ($this->m_packetSize > self::PACKET_BUFFER_SIZE)) 
		{
			return 0;
		}
		if ($arr ['Iden1'] != ord ( 'H' ) || $arr ['Iden2'] != ord ( 'S' )) 
		{
			return - 1;
		}
		
		$this->m_CmdType = $arr ['CmdType'];
		if ($this->m_CmdType <= 0 || $this->m_CmdType > 39321) 
		{
			return - 3;
		}
		$this->m_packetBuffer = substr ( $this->m_packetBuffer, self::PACKET_HEADER_SIZE );
		return 1;
	}
	/**
	 * 设置包内容
	 * 
	 * @param string $packet_buff
	 *        	内容
	 * @param int $packet_size
	 *        	长度
	 */
	public function SetRecvPacketBuffer($packet_buff, $packet_size) 
	{
		$this->m_packetBuffer = $packet_buff;
		$this->m_packetSize = $packet_size;
	}
	
	/**
	 * 读取一个INT类型值
	 * 
	 * @return int 值
	 */
	public function ReadInt() 
	{
		$temp = substr ( $this->m_packetBuffer, 0, 4 );
		$value = unpack ( "N", $temp );
		$this->m_packetBuffer = substr ( $this->m_packetBuffer, 4 );
		return $value [1];
	}
	
	/**
	 * 读取一个无符号INT类型值
	 * 
	 * @return int 值
	 */
	public function ReadUInt() 
	{
		$temp = substr ( $this->m_packetBuffer, 0, 4 );
		list ( , $var_unsigned ) = unpack ( "N", $temp );
		return floatval ( sprintf ( "%u", $var_unsigned ) );
	}
	
	/**
	 * 读取一个Short类型值
	 * 
	 * @return int 值
	 */
	public function ReadShort() 
	{
		$temp = substr ( $this->m_packetBuffer, 0, 2 );
		$value = unpack ( "n", $temp );
		$this->m_packetBuffer = substr ( $this->m_packetBuffer, 2 );
		return $value [1];
	}
	
	/**
	 * 读取一个字符串类型值
	 * 
	 * @return string 值
	 */
	public function ReadString() 
	{
		$len = $this->ReadInt ();
		$value = substr ( $this->m_packetBuffer, 0, $len - 1 );
		$this->m_packetBuffer = substr ( $this->m_packetBuffer, $len );
		return $value;
	}
	/**
	 * 读取64位int
	 * 
	 * @return boolean
	 */
	public function ReadInt64() 
	{
		$temp = substr ( $this->m_packetBuffer, 0, 8 );
		$this->m_packetBuffer = substr ( $this->m_packetBuffer, 8 );
		$hNum = unpack ( "N", substr ( $temp, 0, 4 ) );
		$hNum = $hNum [1] << 32;
		$tNum = unpack ( "N", substr ( $temp, 4, 8 ) );
		$total = $hNum | $tNum [1];
		return $total;
	}
	/**
	 * 读取一个BYTE类型值
	 * 
	 * @return int 值
	 */
	public function ReadByte() 
	{
		$temp = substr ( $this->m_packetBuffer, 0, 1 );
		$value = unpack ( "C", $temp );
		$this->m_packetBuffer = substr ( $this->m_packetBuffer, 1 );
		return $value [1];
	}
}

